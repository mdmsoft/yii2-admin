<?php

use mdm\admin\components\Configs;

/* @var $this tests\codeception\fixtures\AdminFixture */

$migrations = [
    'm140506_102106_rbac_init' => '@yii/rbac/migrations',
    'm140602_111327_create_menu_table' => '@mdm/admin/migrations',
    'm160312_050000_create_user' => '@mdm/admin/migrations',
];

$command = $this->db->createCommand();
$schema = $this->db->schema;

// rbac
$authManager = Yii::$app->getAuthManager();

if ($this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib') {
    $sql = <<<SQL
IF EXISTS (
    SELECT *
    FROM sys.objects
    WHERE [type] = 'TR' AND [name] = 'blablabla'
    )
    DROP dbo.trigger_auth_item_child;
GO
SQL;
    $command->setSql($sql)->execute();
}

if ($schema->getTableSchema($authManager->assignmentTable)) {
    $command->dropTable($authManager->assignmentTable)->execute();
}
if ($schema->getTableSchema($authManager->itemChildTable)) {
    $command->dropTable($authManager->itemChildTable)->execute();
}
if ($schema->getTableSchema($authManager->itemTable)) {
    $command->dropTable($authManager->itemTable)->execute();
}
if ($schema->getTableSchema($authManager->ruleTable)) {
    $command->dropTable($authManager->ruleTable)->execute();
}

// mdm/admin
if ($schema->getTableSchema(Configs::menuTable())) {
    $command->dropTable(Configs::menuTable())->execute();
}
if ($schema->getTableSchema(Configs::userTable())) {
    $command->dropTable(Configs::userTable())->execute();
}

foreach ($migrations as $migration => $path) {
    $file = Yii::getAlias($path . '/' . $migration . '.php');
    include_once $file;
    $migration = new $migration();
    $migration->up();
}