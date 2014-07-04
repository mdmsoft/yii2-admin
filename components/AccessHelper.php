<?php

namespace mdm\admin\components;

use Yii;
use yii\helpers\Inflector;
use yii\caching\GroupDependency;
use ReflectionClass;
use mdm\admin\models\Menu;

/**
 * Description of AccessHelper
 *
 * @author MDMunir
 */
class AccessHelper
{
    const FILE_GROUP = 'file';
    const AUTH_GROUP = 'auth';
    const ROUTE_RULE_NAME = 'route_rule';

    public static function setDefaultRouteRule()
    {
        if (Yii::$app->authManager->getRule(self::ROUTE_RULE_NAME) === null) {
            Yii::$app->authManager->add(Yii::createObject([
                    'class' => RouteRule::className(),
                    'name' => self::ROUTE_RULE_NAME]
            ));
        }
    }

    /**
     * 
     * @return array
     */
    public static function getRoutes($refresh = false)
    {
        $key = static::buildKey(__METHOD__);
        if ($refresh || ($cache = Yii::$app->getCache()) === null || ($result = $cache->get($key)) === false) {
            $result = [];
            self::getRouteRecrusive(Yii::$app, $result);
            if ($cache !== null) {
                $cache->set($key, $result, 0, new GroupDependency([
                    'group' => static::getGroup(self::FILE_GROUP)
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
    private static function getRouteRecrusive($module, &$result)
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
        $result[] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
    }

    private static function getControllerRoutes($module, $namespace, $prefix, &$result)
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
                self::getControllerRoutes($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $id = Inflector::camel2id(substr(basename($file), 0, -14));
                $className = $namespace . Inflector::id2camel($id) . 'Controller';
                if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                    $controller = Yii::createObject($className, [$prefix . $id, $module]);
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
    private static function getActionRoutes($controller, &$result)
    {
        $prefix = '/' . $controller->uniqueId . '/';
        foreach ($controller->actions() as $id => $value) {
            $result[] = $prefix . $id;
        }
        $class = new ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                $result[] = $prefix . Inflector::camel2id(substr($name, 6));
            }
        }
    }

    public static function getSavedRoutes($refresh = false)
    {
        $key = static::buildKey(__METHOD__);
        if ($refresh || ($cache = Yii::$app->getCache()) === null || ($result = $cache->get($key)) === false) {
            $result = [];
            foreach (Yii::$app->getAuthManager()->getPermissions() as $name => $value) {
                if ($name[0] === '/' && substr($name, -1) != '*') {
                    $result[] = $name;
                }
            }
            if ($cache !== null) {
                $cache->set($key, $result, 0, new GroupDependency([
                    'group' => static::getGroup(self::AUTH_GROUP)
                ]));
            }
        }
        return $result;
    }

    /**
     * 
     * @param mixed $userId
     * @param \Closure $callback function($menu){}
     * @param boolean $refresh
     * @return array
     * 
     */
    public static function getAssignedMenu($userId, $callback = null, $refresh = false)
    {
        $key = static::buildKey([__METHOD__, $userId]);
        if ($refresh || ($cache = Yii::$app->getCache()) === null || ($result = $cache->get($key)) === false) {
            $manager = \Yii::$app->getAuthManager();
            $routes = $filter1 = $filter2 = [];
            foreach ($manager->getPermissionsByUser($userId) as $name => $value) {
                if ($name[0] === '/') {
                    if (substr($name, -2) === '/*') {
                        $name = substr($name, 0, -1);
                    }
                    $routes[] = $name;
                }
            }
            $prefix = '\\';
            sort($routes);
            foreach ($routes as $route) {
                if (strpos($route, $prefix) !== 0) {
                    if (substr($route, -1) === '/') {
                        $prefix = $route;
                        $filter1[] = $route . '%';
                    } else {
                        $filter2[] = $route;
                    }
                }
            }
            $assigned = [];
            $query = Menu::find()->select(['id'])->asArray();
            if (count($filter2)) {
                $assigned = $query->where(['route' => $filter2])->column();
            }
            if (count($filter1)) {
                $query->where('route like :filter');
                foreach ($filter1 as $filter) {
                    $assigned = array_merge($assigned, $query->params([':filter' => $filter])->column());
                }
            }
            $menus = Menu::find()->asArray()->indexBy('id')->all();
            $assigned = static::requiredParent($assigned, $menus);
            $result = static::normalizeMenu($assigned, $menus, $callback);
            if (isset($cache)) {
                $cache->set($key, $result, 0, new GroupDependency([
                    'group' => static::getGroup(self::AUTH_GROUP)
                ]));
            }
        }
        return $result;
    }

    private static function requiredParent($assigned, &$menus)
    {
        $l = count($assigned);
        for ($i = 0; $i < $l; $i++) {
            $id = $assigned[$i];
            $parent_id = $menus[$id]['parent'];
            if ($parent_id !== null && !in_array($parent_id, $assigned)) {
                $assigned[$l++] = $parent_id;
            }
        }
        return $assigned;
    }

    private static function normalizeMenu(&$assigned, &$menus, $callback, $parent = null)
    {
        $result = [];
        $order = [];
        foreach ($assigned as $id) {
            $menu = $menus[$id];
            if ($menu['parent'] == $parent) {
                $menu['children'] = static::normalizeMenu($assigned, $menus, $callback, $id);
                if ($callback !== null) {
                    $item = call_user_func($callback, $menu);
                } else {
                    if (!empty($menu['route'])) {
                        $url = [];
                        $r = explode('&', $menu['route']);
                        $url[0] = $r[0];
                        unset($r[0]);
                        foreach ($r as $part) {
                            $part = explode('=', $part);
                            $url[$part[0]] = isset($part[1]) ? $part[1] : '';
                        }
                    } else {
                        $url = '#';
                    }
                    $item = [
                        'label' => $menu['name'],
                        'url' => $url,
                    ];
                    if ($menu['children'] != []) {
                        $item['items'] = $menu['children'];
                    }
                }
                $result[] = $item;
                $order[] = $menu['order'];
            }
        }
        if ($result != []) {
            array_multisort($order, $result);
        }
        return $result;
    }

    private static function getGroup($group)
    {
        return md5(serialize([__CLASS__, $group]));
    }

    private static function buildKey($key)
    {
        return[
            __CLASS__,
            $key
        ];
    }

    public static function refeshFileCache()
    {
        if (($cache = Yii::$app->getCache()) !== null) {
            GroupDependency::invalidate($cache, static::getGroup(self::FILE_GROUP));
        }
    }

    public static function refeshAuthCache()
    {
        if (($cache = Yii::$app->getCache()) !== null) {
            GroupDependency::invalidate($cache, static::getGroup(self::AUTH_GROUP));
        }
    }
}