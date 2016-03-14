<?php
require_once __DIR__ . '/_beforeRequest.php';
/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'mdm-admin-test',
    'basePath' => dirname(dirname(__DIR__)), // @test
    'vendorPath' => dirname(dirname(dirname(__DIR__))) . '/vendor',
    'language' => 'en-US',
    'aliases' => [
        '@mdm/admin' => dirname(dirname(dirname(__DIR__))),
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
        ]
    ],
    'controllerMap' => [
//        'fixture' => [
//            'class' => 'yii\faker\FixtureController',
//            'fixtureDataPath' => '@tests/codeception/fixtures',
//            'templatePath' => '@tests/codeception/templates',
//            'namespace' => 'tests\codeception\fixtures',
//        ],
        'migrate'=>[
            'class'=>'yii\console\controllers\MigrateController',
            'migrationPath' => '@yii/rbac/migrations'
        ]
    ],
    'components' => [
        'db' => require(__DIR__ . '/db.php'),
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache'
        ]
    ],
    'on beforeRequest' => 'onBeforeRequestTest'
];
