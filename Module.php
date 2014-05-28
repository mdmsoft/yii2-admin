<?php

namespace mdm\admin;

use mdm\admin\components\AccessControl;

/**
 * Description of Module
 *
 * @author MDMunir
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     *
     * @var string 
     */
    public $appLayout = '@app/views/layouts/main.php';

    /**
     *
     * @var array 
     */
    public $allowActions = [];

    public function init()
    {
        parent::init();
    }

    /**
     * 
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        $app->attachBehavior(AccessControl::className(), new AccessControl($this));
    }
}