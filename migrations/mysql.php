<?php

use yii\db\Schema;

$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
return [
	['createTable', 'tbl_auth_item', [
			'name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'type' => Schema::TYPE_STRING . ' NOT NULL',
			'description' => Schema::TYPE_TEXT,
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
			'primary key (`name`)',
			'key `type` (`type`)'
		], $tableOptions,
	],
	['createTable', 'tbl_auth_item_child', [
			'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
			'child' => Schema::TYPE_STRING . '(64) NOT NULL',
			'primary key (`parent`,`child`)',
			'foreign key (`parent`) references `tbl_auth_item` (`name`) on delete cascade on update cascade',
			'foreign key (`child`) references `tbl_auth_item` (`name`) on delete cascade on update cascade'
		], $tableOptions,
	],
	['createTable', 'tbl_auth_assignment', [
			'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'user_id' => Schema::TYPE_STRING . '(64) NOT NULL',
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
			'primary key (`item_name`,`user_id`)',
			'foreign key (`item_name`) references `tbl_auth_item` (`name`) on delete cascade on update cascade',
		], $tableOptions,
	],
];