<?php

use yii\db\Schema;

return [
	['createTable', 'tbl_auth_item', [
			'name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'type' => Schema::TYPE_STRING . ' NOT NULL',
			'description' => Schema::TYPE_TEXT,
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
		],
	],
	['createTable', 'tbl_auth_item_child', [
			'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
			'child' => Schema::TYPE_STRING . '(64) NOT NULL',
		],
	],
	['createTable', 'tbl_auth_assignment', [
			'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'user_id' => Schema::TYPE_STRING . '(64) NOT NULL',
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
		],
	],
	['addPrimaryKey', 'tbl_auth_item_pk', 'tbl_auth_item', 'name'],
	['addPrimaryKey', 'tbl_auth_item_child_pk', 'tbl_auth_item_child', ['parent', 'child']],
	['addForeignKey', 'tbl_auth_item_child_fk1', 'tbl_auth_item_child', 'parent', 'tbl_auth_item', 'name', 'CASCADE', 'CASCADE'],
	['addForeignKey', 'tbl_auth_item_child_fk2', 'tbl_auth_item_child', 'child', 'tbl_auth_item', 'name', 'CASCADE', 'CASCADE'],
	['addPrimaryKey', 'tbl_auth_assignment_pk', 'tbl_auth_assignment', ['item_name', 'user_id']],
	['addForeignKey', 'tbl_auth_assignment_fk', 'tbl_auth_assignment', 'item_name', 'tbl_auth_item', 'name', 'CASCADE', 'CASCADE'],
];