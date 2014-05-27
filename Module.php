<?php

namespace mdm\admin;

use mdm\admin\components\AccessControl;
use mdm\admin\components\AccessDependency;
use yii\caching\Cache;
use yii\di\Instance;

/**
 * Description of Module
 *
 * @author MDMunir
 * 
 * @property Cache $cache
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $appLayout = '@app/views/layouts/main.php';

    /**
     *
     * @var array 
     */
    public $allowActions = [];

    /**
     *
     * @var Cache 
     */
    public $cache = 'cache';

    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, Cache::className());
    }

    /**
     * 
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        $app->attachBehavior(AccessControl::className(), new AccessControl($this));
    }

    public function resetCache()
    {
        if ($this->cache !== null) {
            AccessDependency::resetDependency($this->cache);
        }
    }
}