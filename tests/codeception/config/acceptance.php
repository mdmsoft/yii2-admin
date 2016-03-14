<?php
/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/config.php'),
    require(__DIR__ . '/web.php'),
    [

    ]
);
