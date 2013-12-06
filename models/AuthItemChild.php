<?php

namespace mdm\admin\models;

/**
 * This is the model class for table "tbl_auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parent
 * @property AuthItem $child
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tbl_auth_item_child';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['parent', 'child'], 'required'],
			[['parent', 'child'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'parent' => 'Parent',
			'child' => 'Child',
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getParent()
	{
		return $this->hasOne(AuthItem::className(), ['name' => 'parent']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getChild()
	{
		return $this->hasOne(AuthItem::className(), ['name' => 'child']);
	}
}
