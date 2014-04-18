<?php

namespace mdm\admin\components;

use Yii;
use yii\helpers\Inflector;
use yii\rbac\Item;

/**
 * Description of AccessHelper
 *
 * @author MDMunir
 */
class AccessHelper
{

    const CACHE_USER_ROUTES_KEY = '_USER_ROUTES';

    public static function getUserRoutes($userId)
    {
        $key = [static::CACHE_USER_ROUTES_KEY, $userId];
        if (($cache = Yii::$app->cache) === null || ($data = $cache->get($key))===false) {
            $result = [];
            foreach (Yii::$app->authManager->getPermissionsByUser($userId) as $name => $permission) {
                if ($name[0] === '/') {
                    $result[] = substr($name, 1);
                }
            }
            if($cache !== null){
                $cache->set($key, $result, 0, new AccessDependency());
            }
            return $result;
        }
        return $data;
    }

    /**
     * 
     * @return array
     */
    public static function getRoutes()
    {
        $result = [];
        self::getRouteRecrusive(Yii::$app, $result);
        return $result;
    }

    /**
     * 
     * @param \yii\base\Module $module
     * @param array $result
     */
    protected static function getRouteRecrusive($module, &$result)
    {
        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                self::getRouteRecrusive($child, $result);
            }
        }
        /* @var $controller \yii\base\Controller */
        foreach ($module->controllerMap as $id => $value) {
            $controller = Yii::createObject($value, [$id, $module]);
            self::getActionRoutes($controller, $result);
            $result[] = '/' . $controller->uniqueId . '/*';
        }

        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        self::getControllerRoutes($module, $namespace, '', $result);
        $result[] = $module->uniqueId . '/*';
    }

    protected static function getControllerRoutes($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                self::getControllerRoutes($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $id = Inflector::camel2id(substr(basename($file), 0, -14));
                $className = $namespace . Inflector::id2camel($id) . 'Controller';
                if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                    $controller = new $className($prefix . $id, $module);
                    self::getActionRoutes($controller, $result);
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
    protected static function getActionRoutes($controller, &$result)
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

}
