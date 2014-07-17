<?php

namespace mdm\admin\components;

use Yii;
use yii\caching\TagDependency;
use mdm\admin\models\Menu;

/**
 * Description of MenuHelper
 *
 * @author MDMunir
 */
class MenuHelper
{
    const CACHE_TAG = 'mdm.admin.menu';

    /**
     * 
     * @param mixed $userId
     * @param integer $root
     * @param \Closure $callback function($menu){}
     * @param boolean $refresh
     * @return array
     * 
     */
    public static function getAssignedMenu($userId, $root = null, $callback = null, $refresh = false)
    {
        $config = Configs::instance();
        
        $key = [__METHOD__, $userId, $root];
        $refresh = $refresh || $callback !== null;
        $cache = $config->cache;
        
        if ($refresh || ($result = $cache->get($key)) === false) {
            $manager = Yii::$app->getAuthManager();
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
            $result = static::normalizeMenu($assigned, $menus, $callback, $root);
            if (!$refresh && $cache !== null) {
                $cache->set($key, $result, 0, new TagDependency([
                    'tags' => self::CACHE_TAG
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

    public static function invalidate()
    {
        if (Configs::instance()->cache !== null) {
            TagDependency::invalidate(Configs::instance()->cache, self::CACHE_TAG);
        }
    }
}