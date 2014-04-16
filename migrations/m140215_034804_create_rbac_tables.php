<?php

class m140215_034804_create_rbac_tables extends \yii\db\Migration
{

	public function safeUp()
	{
		switch ($driver = $this->db->driverName) {
			case 'mysql':
			case 'pgsql':
				$actions = require __DIR__ . "/{$driver}.php";
				break;

			default:
				$actions = require __DIR__ . "/default.php";
				break;
		}
		foreach ($actions as $action) {
			$method = array_shift($action);
			call_user_func_array([$this, $method], $action);
		}
		return true;
	}

	public function safeDown()
	{
		$this->dropTable('tbl_auth_item_child');
		$this->dropTable('tbl_auth_assignment');
		$this->dropTable('tbl_auth_item');
		return true;
	}

}
