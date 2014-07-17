<?php

namespace mdm\admin\components;

use Yii;
use yii\db\Connection;
use yii\caching\Cache;
use yii\helpers\ArrayHelper;

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
        if ($this->db !== null && !($this->db instanceof Connection)) {
            if (is_string($this->db) && strpos($this->db, '\\') === false) {
                $this->db = Yii::$app->get($this->db, false);
            } else {
                $this->db = Yii::createObject($this->db);
            }
        }
        if ($this->cache !== null && !($this->cache instanceof Cache)) {
            if (is_string($this->cache) && strpos($this->cache, '\\') === false) {
                $this->cache = Yii::$app->get($this->cache, false);
            } else {
                $this->cache = Yii::createObject($this->cache);
            }
        }
        parent::init();
    }

    /**
     * 
     * @return self
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            $type = ArrayHelper::getValue(Yii::$app->params, 'mdm.admin.configs', []);
            if (is_array($type) && !isset($type['class'])) {
                $type['class'] = self::className();
            }
            return self::$_instance = Yii::createObject($type);
        }
        return self::$_instance;
    }
}