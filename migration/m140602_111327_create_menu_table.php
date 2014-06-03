<?php

use yii\db\Schema;

class m140602_111327_create_menu_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%menu}}', [
            'menu_id'=>  Schema::TYPE_PK,
            'menu_name'=>  Schema::TYPE_STRING.'(128) NOT NULL',
            'menu_parent'=>  Schema::TYPE_INTEGER,
            'menu_route'=>  Schema::TYPE_STRING.'(256)',
            'FOREIGN KEY (menu_parent) REFERENCES {{%menu}}(menu_id) ON DELETE SET NULL ON UPDATE CASCADE',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%menu}}');
    }
}
