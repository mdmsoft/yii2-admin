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

    /**
     * 
     * @return string[] 
     */
    public static function getItemsRole()
    {
        $manager = Yii::$app->getAuthManager();
        if (Yii::$app->user->getIsGuest()) {
            $items = $manager->getItemChildren('guest');
        } else {
            $items = $manager->getItems(Yii::$app->user->id);
        }
        if (count($items) > 0) {
            $key_cache = array_keys($items);
            $key_cache[] = '__ItemsRole__';
            $cache = Yii::$app->getCache();
            $result = $cache ? $cache->get($key_cache) : false;
            if ($result === false) {
                $result = self::getMenuItemRecrusive($items);
                $cache && $cache->set($key_cache, $result, 0, new AccessDependency('role'));
            }
            return $result;
        }
        return [];
    }

    /**
     * 
     * @param \yii\rbac\Item[] $items
     * @return string[]
     */
    protected static function getMenuItemRecrusive($items)
    {
        $result = [];
        /* @var $item yii\rbac\Item */
        foreach ($items as $name => $item) {
            $result[] = $name;
            if (($_items = $item->getChildren()) != [] && ($_result = self::getMenuItemRecrusive($_items)) != []) {
                foreach ($_result as $_item) {
                    $result[] = $_item;
                }
            }
        }
        return $result;
    }

    public static function getRoutes($module = null)
    {
        $result = [];
        if ($module === null) {
            $module = Yii::$app;
        }

        $result[] = $module instanceof \yii\base\Application ? '*' : $module->uniqueId . '/*';

        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) === null) {
                continue;
            }

            foreach (self::getRoutes($child) as $route) {
                $result[] = $route;
            }
        }
        /* @var $controller \yii\base\Controller */
        foreach ($module->controllerMap as $id => $value) {
            $controller = Yii::createObject($value, $id, $module);
            $result[] = $controller->uniqueId . '/*';
            foreach (self::getActions($controller) as $route) {
                $result[] = $route;
            }
        }

        $path = $module->getControllerPath();
        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        foreach (self::getControllers($module, $path, '', $namespace) as $route) {
            $result[] = $route;
        }
        return $result;
    }

    private static function getControllers($module, $path, $prefixId, $namespace)
    {
        $result = [];
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                foreach (self::getControllers($module, $path . '/' . $file, $prefixId . $file . '/', $namespace . $file . '\\') as $route) {
                    $result[] = $route;
                }
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $id = Inflector::camel2id(substr(basename($file), 0, -14));
                $className = $namespace . Inflector::id2camel($id) . 'Controller';
                if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                    $controller = new $className($prefixId . $id, $module);
                    $result[] = $controller->uniqueId . '/*';
                    foreach (self::getActions($controller) as $route) {
                        $result[] = $route;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param \yii\base\Controller $controller
     * @return mixed List of all controller action.
     */
    protected static function getActions($controller)
    {
        $result = [];
        $prefix = $controller->uniqueId . '/';
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
        return $result;
    }

    public static function getAvaliableRoutes()
    {
        $routes = self::getRoutes();
        $result = ['task' => [], 'operation' => []];
        foreach ($routes['task'] as $route) {
            $result['task'][$route] = $route;
        }
        foreach ($routes['operation'] as $route) {
            $result['operation'][$route] = $route;
        }
        return $result;
    }

    public static function getAvaliableChild($type = Item::TYPE_ROLE)
    {
        $result = ['role' => [], 'task' => [], 'operation' => []];
        foreach (Yii::$app->authManager->getItems() as $item) {
            if ($item->type > $type) {
                continue;
            }
            switch ($item->type) {
                case Item::TYPE_OPERATION:
                    $result['operation'][$item->name] = $item->name;
                    break;

                case Item::TYPE_TASK:
                    $result['task'][$item->name] = $item->name;
                    break;

                case Item::TYPE_ROLE:
                    $result['role'][$item->name] = $item->name;
                    break;
            }
        }
        return $result;
    }

}
