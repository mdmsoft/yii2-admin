<?php

namespace mdm\auth\models;

use yii\rbac\Item;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $biz_rule
 * @property string $data
 *
 * @property AuthAssignment $authAssignment
 * @property User[] $users
 * @property AuthItem[] $authItemChildren
 * @property AuthItem[] $authItemParents
 */
class AuthItem extends \yii\db\ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tbl_auth_item';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'type'], 'required'],
			[['type'], 'integer'],
			[['description', 'biz_rule', 'data'], 'string'],
			[['name'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => 'Name',
			'type' => 'Type',
			'description' => 'Description',
			'biz_rule' => 'Biz Rule',
			'data' => 'Data',
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getAuthAssignment()
	{
		return $this->hasOne(AuthAssignment::className(), ['item_name' => 'name']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('tbl_auth_assignment', ['item_name' => 'name']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getAuthItemChildren()
	{
		return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('tbl_auth_item_child', ['child' => 'name']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getAuthItemParents()
	{
		return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('tbl_auth_item_child', ['child' => 'parent']);
	}

	public static function getTypeName($type=null)
	{
		$result = [
			Item::TYPE_OPERATION => 'Operation',
			Item::TYPE_TASK => 'Task',
			Item::TYPE_ROLE => 'Role'
		];
		if($type === null)
			return $result;
		return $result[$type];
	}

}
