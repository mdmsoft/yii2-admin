<?php

namespace mdm\admin\items;

use Yii;
use mdm\admin\models\Route;
use mdm\admin\components\MenuHelper;
use yii\caching\TagDependency;
use yii\web\Response;
use yii\helpers\Html;
use mdm\admin\components\RouteRule;
use mdm\admin\components\Configs;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use Exception;

class RouteController extends \yii\web\Controller
{
    const CACHE_TAG = 'mdm.admin.route';

    public function actionIndex()
    {
        $manager = Yii::$app->getAuthManager();

        $exists = $existsOptions = $routes = [];
        foreach ($this->getAppRoutes() as $route) {
            $routes[$route] = $route;
        }
        $allRoutes = $routes;
        foreach ($manager->getPermissions() as $name => $permission) {
            if ($name[0] !== '/') {
                continue;
            }
            $exists[$name] = $name;
            if (isset($allRoutes[$name])) {
                unset($routes[$name]);
            } else {
                $r = explode('&', $name);
                if (!isset($allRoutes[$r[0]])) {
                    $existsOptions[$name] = ['class' => 'lost'];
                }
            }
        }

        return $this->render('index', ['new' => $routes, 'exists' => $exists, 'existsOptions' => $existsOptions]);
    }

    public function actionCreate()
    {
        $model = new Route;
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($model->validate()) {
                $routes = preg_split('/\s*,\s*/', trim($model->route), -1, PREG_SPLIT_NO_EMPTY);
                $this->saveNew($routes);
                MenuHelper::invalidate();
                $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionAssign($action)
    {
        $post = Yii::$app->getRequest()->post();
        $routes = $post['routes'];
        $manager = Yii::$app->getAuthManager();
        $error = [];
        if ($action == 'assign') {
            $this->saveNew($routes);
        } else {
            foreach ($routes as $route) {
                $child = $manager->getPermission($route);
                try {
                    $manager->remove($child);
                } catch (Exception $e) {
                    $error[] = $exc->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        return [$this->actionRouteSearch('new', $post['search_av']),
            $this->actionRouteSearch('exists', $post['search_asgn']),
            $error];
    }

    public function actionRouteSearch($target, $term = '', $refresh = '0')
    {
        if ($refresh == '1') {
            $this->invalidate();
        }
        $result = [];
        $manager = Yii::$app->getAuthManager();

        $existsOptions = [];
        $exists = array_keys($manager->getPermissions());
        $routes = $this->getAppRoutes();
        if ($target == 'new') {
            foreach ($routes as $route) {
                if (in_array($route, $exists)) {
                    continue;
                }
                if (empty($term) or strpos($route, $term) !== false) {
                    $result[$route] = $route;
                }
            }
        } else {
            foreach ($exists as $name) {
                if ($name[0] !== '/') {
                    continue;
                }
                if (empty($term) or strpos($name, $term) !== false) {
                    $result[$name] = $name;
                }

                // extract route part from $name
                $r = explode('&', $name);
                if (empty($r[0]) || !in_array($r[0], $routes)) {
                    $existsOptions[$name] = ['class' => 'lost'];
                }
            }
        }
        $options = $target == 'new' ? [] : ['options' => $existsOptions];

        return Html::renderSelectOptions('', $result, $options);
    }

    private function saveNew($routes)
    {
        $manager = Yii::$app->getAuthManager();
        foreach ($routes as $route) {
            try {
                $r = explode('&', $route);
                $item = $manager->createPermission('/' . trim($route, '/'));
                if (count($r) > 1) {
                    $action = '/' . trim($r[0], '/');
                    if (($itemAction = $manager->getPermission($action)) === null) {
                        $itemAction = $manager->createPermission($action);
                        $manager->add($itemAction);
                    }
                    unset($r[0]);
                    foreach ($r as $part) {
                        $part = explode('=', $part);
                        $item->data['params'][$part[0]] = isset($part[1]) ? $part[1] : '';
                    }
                    $this->setDefaultRule();
                    $item->ruleName = RouteRule::RULE_NAME;
                    $manager->add($item);
                    $manager->addChild($item, $itemAction);
                } else {
                    $manager->add($item);
                }
            } catch (Exception $e) {

            }
        }
    }

    public function getAppRoutes()
    {
        $key = __METHOD__;
        $cache = Configs::instance()->cache;
        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
            $this->getRouteRecrusive(Yii::$app, $result);
            if ($cache !== null) {
                $cache->set($key, $result, Configs::instance()->cacheDuration, new TagDependency([
                    'tags' => self::CACHE_TAG
                ]));
            }
        }

        return $result;
    }

    /**
     *
     * @param \yii\base\Module $module
     * @param array            $result
     */
    private function getRouteRecrusive($module, &$result)
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecrusive($child, $result);
                }
            }

            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result);
            $result[] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    private function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    private function getControllerActions($type, $id, $module, &$result)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $result[] = '/' . $controller->uniqueId . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     *
     * @param \yii\base\Controller $controller
     * @param Array                $result     all controller action.
     */
    private function getActionRoutes($controller, &$result)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $result[] = $prefix . Inflector::camel2id(substr($name, 6));
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    protected function invalidate()
    {
        if (Configs::instance()->cache !== null) {
            TagDependency::invalidate(Configs::instance()->cache, self::CACHE_TAG);
        }
    }

    public function setDefaultRule()
    {
        if (Yii::$app->authManager->getRule(RouteRule::RULE_NAME) === null) {
            Yii::$app->authManager->add(Yii::createObject([
                    'class' => RouteRule::className(),
                    'name' => RouteRule::RULE_NAME]
            ));
        }
    }
}
