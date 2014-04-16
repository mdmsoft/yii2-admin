<?php

use yii\db\Schema;

return [
	['createTable', '{{%auth_item}}', [
			'name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'type' => Schema::TYPE_STRING . ' NOT NULL',
			'description' => Schema::TYPE_TEXT,
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
			'primary key ("name")',
		],
	],
	['execute', 'create index {{%auth_item_type_idx}} on "{{%auth_item}}" ("type")'],
	['createTable', '{{%auth_item_child}}', [
			'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
			'child' => Schema::TYPE_STRING . '(64) NOT NULL',
			'primary key ("parent","child")',
			'foreign key ("parent") references "{{%auth_item}}" ("name") on delete cascade on update cascade',
			'foreign key ("child") references "{{%auth_item}}" ("name") on delete cascade on update cascade'
		],
	],
	['createTable', '{{%auth_assignment}}', [
			'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
			'user_id' => Schema::TYPE_STRING . '(64) NOT NULL',
			'biz_rule' => Schema::TYPE_TEXT,
			'data' => Schema::TYPE_TEXT,
			'primary key ("item_name","user_id")',
			'foreign key ("item_name") references "{{%auth_item}}" ("name") on delete cascade on update cascade',
		],
	],
];