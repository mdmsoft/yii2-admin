<?php
/**
 * This is the configuration file for the yii2-admin unit tests.
 * You can override configuration values by creating a `db-local.php` file
 * and manipulate the `$databases` variable.
 * For example to change MySQL username and password your `db-local.php` should
 * contain the following:
 *
  <?php
  $driver = 'pgsql';
  $databases[$driver]['username'] = 'myname';
  $databases[$driver]['password'] = 'changeme';

 */
$databases = [
    'mysql' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mdm_admin_test',
        'username' => 'travis',
        'password' => '',
    ],
    'sqlite' => [
        //'dsn' => 'sqlite::memory:',
        'dsn' => 'sqlite:@runtime/mdm_admin_test.sqlite',
    ],
    'pgsql' => [
        'dsn' => 'pgsql:host=localhost;dbname=mdm_admin_test;port=5432;',
        'username' => 'postgres',
        'password' => 'postgres',
    ],
];

$driver = 'mysql';
if (is_file(__DIR__ . '/db-local.php')) {
    include __DIR__ . '/db-local.php';
}
return array_merge(['class' => 'yii\db\Connection'], $databases[$driver]);
