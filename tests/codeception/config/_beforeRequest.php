<?php

use yii\db\Connection;

if (!function_exists('onBeforeRequestTest')) {

    function onBeforeRequestTest($event)
    {
        /* @var $app yii\base\Application */
        $app = $event->sender;
        $databases = require(__DIR__ . '/db.php');
        foreach ($databases as $config) {
            try {
                $db = new Connection($config);
                $db->open();
                $app->set('db', $db);
                return;
            } catch (Exception $exc) {
                
            }
        }
    }
}
