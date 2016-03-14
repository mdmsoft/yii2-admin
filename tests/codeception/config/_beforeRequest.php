<?php

if (!function_exists('onBeforeRequestTest')) {

    function onBeforeRequestTest($event)
    {
        /* @var $app yii\base\Application */
        $app = $event->sender;

        ob_start();
        ob_implicit_flush(false);
        $app->runAction('migrate');
        ob_get_clean();

        $app->getAuthManager()->removeAll();
    }
}
