<?php

namespace mdm\admin\controllers;

use Yii;
use mdm\admin\classes\MenuHelper;
use yii\caching\TagDependency;
use yii\rest\Controller;
use mdm\admin\classes\RouteRule;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use Exception;

/**
 * Description of RuleController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RouteController extends Controller
{
    const CACHE_TAG = 'mdm.admin.route';

    protected function verbs()
    {
        return[
            'index' => ['GET', 'HEAD'],
            'add' => ['POST'],
            'remove' => ['POST'],
        ];
    }

    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex($refresh = false)
    {
        $routes = array_filter(Yii::$app->getAuthManager()->getPermissions(), function($item) {
            return $item->name[0] == '/';
        });
        $avaliables = [];
        foreach ($this->getAppRoutes($refresh) as $name) {
            if (!isset($routes[$name])) {
                $avaliables[] = [
                    'name' => $name,
                ];
            }
        }

        return[
            'routes' => array_values($routes),
            'avaliables' => $avaliables,
        ];
    }

    /**
     * Assign or remove items
     * @param string $action
     * @return array
     */
    public function actionAdd()
    {
        $routes = Yii::$app->getRequest()->post('items', []);
        $error = [];
        $count = 0;
        foreach ($routes as $route) {
            try {
                $this->addRoute($route);
                $count++;
            } catch (Exception $exc) {
                $error[] = $exc->getMessage();
            }
        }

        MenuHelper::invalidate();
        return[
            'type' => 'S',
            'count' => $count,
            'errors' => $error,
        ];
    }

    /**
     * Assign or remove items
     * @param string $action
     * @return array
     */
    public function actionRemove()
    {
        $routes = Yii::$app->getRequest()->post('items', []);
        $manager = Yii::$app->getAuthManager();
        $error = [];
        $count = 0;
        foreach ($routes as $route) {
            $route = $manager->createPermission($route);
            try {
                $manager->remove($route);
                $count++;
            } catch (Exception $exc) {
                $error[] = $exc->getMessage();
            }
        }

        MenuHelper::invalidate();
        return[
            'type' => 'S',
            'count' => $count,
            'errors' => $error,
        ];
    }

    /**
     * Save one or more route(s)
     * @param array $route
     */
    protected function addRoute($route)
    {
        $manager = Yii::$app->getAuthManager();
        $route = '/' . trim($route, ' /');
        $url = parse_url($route);
        $item = $manager->createPermission('/' . trim($route, '/'));
        if (isset($url['query'])) {
            $action = $url['path'];
            if (($itemAction = $manager->getPermission($action)) === null) {
                $itemAction = $manager->createPermission($action);
                $manager->add($itemAction);
            }
            parse_str($url['query'], $params);
            $item->data['params'] = $params;

            $this->setDefaultRule();
            $item->ruleName = RouteRule::RULE_NAME;
            $manager->add($item);
            $manager->addChild($item, $itemAction);
        } else {
            $manager->add($item);
        }
    }

    /**
     * Get list of application routes
     * @return array
     */
    public function getAppRoutes($refresh = false)
    {
        $key = __METHOD__;
        $cache = Yii::$app->getCache();
        if ($refresh || $cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
            $this->getRouteRecrusive(Yii::$app, $result);
            if ($cache !== null) {
                $cache->set($key, $result, 0, new TagDependency([
                    'tags' => self::CACHE_TAG
                ]));
            }
        }

        return $result;
    }

    /**
     * Get route(s) recrusive
     * @param \yii\base\Module $module
     * @param array $result
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

    /**
     * Get list controller under module
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
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

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
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
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
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

    /**
     * Set default rule of parameterize route.
     */
    protected function setDefaultRule()
    {
        if (Yii::$app->authManager->getRule(RouteRule::RULE_NAME) === null) {
            Yii::$app->authManager->add(Yii::createObject(RouteRule::className()));
        }
    }
}