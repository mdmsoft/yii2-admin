<?php

namespace mdm\admin\items;

use Yii;
use mdm\admin\models\Route;
use mdm\admin\components\MenuHelper;
use yii\caching\GroupDependency;
use yii\web\Response;
use yii\helpers\Html;
use mdm\admin\components\RouteRule;
use mdm\admin\components\Configs;
use yii\helpers\Inflector;
use Exception;

class RouteController extends \yii\web\Controller
{

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
        if ($action == 'assign') {
            $this->saveNew($routes);
        } else {
            foreach ($routes as $route) {
                $child = $manager->getPermission($route);
                try {
                    $manager->remove($child);
                } catch (Exception $e) {
                    
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return [$this->actionRouteSearch('new', $post['search_av']),
            $this->actionRouteSearch('exists', $post['search_asgn'])];
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
                $cache->set($key, $result, 0, new GroupDependency([
                    'group' => md5(__CLASS__)
                ]));
            }
        }
        return $result;
    }

    /**
     * 
     * @param \yii\base\Module $module
     * @param array $result
     */
    private function getRouteRecrusive($module, &$result)
    {
        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                $this->getRouteRecrusive($child, $result);
            }
        }
        /* @var $controller \yii\base\Controller */
        foreach ($module->controllerMap as $id => $value) {
            $controller = Yii::createObject($value, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $result[] = '/' . $controller->uniqueId . '/*';
        }

        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        $this->getControllerRoutes($module, $namespace, '', $result);
        $result[] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
    }

    private function getControllerRoutes($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                $this->getControllerRoutes($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $id = Inflector::camel2id(substr(basename($file), 0, -14));
                $className = $namespace . Inflector::id2camel($id) . 'Controller';
                if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                    $controller = Yii::createObject($className, [$prefix . $id, $module]);
                    $this->getActionRoutes($controller, $result);
                    $result[] = '/' . $controller->uniqueId . '/*';
                }
            }
        }
    }

    /**
     * 
     * @param \yii\base\Controller $controller
     * @param Array $result all controller action.
     */
    private function getActionRoutes($controller, &$result)
    {
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
    }

    protected function invalidate()
    {
        if (Configs::instance()->cache !== null) {
            GroupDependency::invalidate(Configs::instance()->cache, md5(__CLASS__));
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