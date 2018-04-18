<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('MDM_ADMIN_BASE_PATH') or define('MDM_ADMIN_BASE_PATH', dirname(dirname(dirname(__DIR__))));

require(MDM_ADMIN_BASE_PATH . '/vendor/autoload.php');
require(MDM_ADMIN_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', dirname(dirname(__DIR__)));
