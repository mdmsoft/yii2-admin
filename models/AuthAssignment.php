<?php

namespace mdm\admin\models;

/**
 * This is the model class for table "tbl_auth_assignment".
 *
 * @property string $item_name
 * @property integer $user_id
 * @property string $biz_rule
 * @property string $data
 *
 * @property User $user
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tbl_auth_assignment';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['item_name', 'user_id'], 'required'],
			[['user_id'], 'integer'],
			[['biz_rule', 'data'], 'string'],
			[['item_name'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'item_name' => 'Item Name',
			'user_id' => 'User ID',
			'biz_rule' => 'Biz Rule',
			'data' => 'Data',
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getItemName()
	{
		return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
	}
}
