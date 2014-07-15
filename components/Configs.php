<?php

namespace mdm\admin\components;

use Yii;
use yii\db\Connection;
use yii\caching\Cache;

/**
 * Description of Configs
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Configs extends \yii\base\Object
{
    /**
     *
     * @var Connection 
     */
    public $db = 'db';

    /**
     *
     * @var Cache 
     */
    public $cache = 'cache';
    
    /**
     *
     * @var self 
     */
    private static $_instance;

    public function init()
    {
        $this->db = $this->db instanceof Connection ? $this->db : Yii::$app->get($this->db, false);
        $this->cache = $this->cache instanceof Cache ? $this->cache : Yii::$app->get($this->cache, false);
        parent::init();
    }

    /**
     * 
     * @return self
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            return self::$_instance = Yii::$container->get(self::className());
        }
        return self::$_instance;
    }
}