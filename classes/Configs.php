<?php

namespace mdm\admin\classes;

use Yii;
use yii\db\Connection;
use yii\caching\Cache;
use yii\helpers\ArrayHelper;
use yii\base\Object;

/**
 * Configs
 * Used for configure some value. To set config you can use [[\yii\base\Application::$params]]
 * 
 * ~~~
 * return [
 *     
 *     'mdm.admin.configs' => [
 *         'db' => 'customDb',
 *         'menuTable' => 'admin_menu',
 *     ]
 * ];
 * ~~~
 * 
 * or use [[\Yii::$container]]
 * 
 * ~~~
 * Yii::$container->set('mdm\admin\classes\Configs',[
 *     'db' => 'customDb',
 *     'menuTable' => 'admin_menu',
 * ]);
 * ~~~
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Configs extends Object
{
    /**
     * @var array
     */
    private $_configs = [
        'db' => 'db',
        'cache' => 'cache',
        'menuTable' => '{{%menu}}'
    ];

    /**
     * @var self Instance of self
     */
    private static $_instance;

    /**
     * Create instance of self
     * @return static
     */
    protected static function instance()
    {
        if (self::$_instance === null) {
            $type = ArrayHelper::getValue(Yii::$app->params, 'mdm.admin.configs', []);
            if (is_array($type) && !isset($type['class'])) {
                $type['class'] = static::className();
            }

            return self::$_instance = Yii::createObject($type);
        }

        return self::$_instance;
    }

    public function __set($name, $value)
    {
        $this->_configs[$name] = $value;
    }

    protected function getDb()
    {
        if ($this->_configs['db'] !== null && !($this->_configs['db'] instanceof Connection)) {
            $this->_configs['db'] = Yii::$app->get($this->_configs['db'], false);
        }
        return $this->_configs['db'];
    }

    protected function getCache()
    {
        if ($this->_configs['cache'] !== null && !($this->_configs['cache'] instanceof Cache)) {
            $this->_configs['cache'] = Yii::$app->get($this->_configs['cache'], false);
        }
        return $this->_configs['cache'];
    }

    protected function getMenuTable()
    {
        return $this->_configs['menuTable'];
    }

    /**
     * @return Connection
     */
    public static function db()
    {
        return static::instance()->getDb();
    }

    /**
     * @return Cache
     */
    public static function cache()
    {
        return static::instance()->getCache();
    }

    /**
     * @return string
     */
    public static function menuTable()
    {
        return static::instance()->getMenuTable();
    }
}