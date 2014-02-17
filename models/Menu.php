<?php

namespace mdm\admin\models;

/**
 * This is the model class for table "tbl_menu".
 *
 * @property string $menu_name
 * @property integer $menu_parent
 * @property string $menu_url
 * @property integer $menu_id
 */
class Menu extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tbl_menu';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['menu_name'], 'required'],
			[['menu_parent'], 'integer'],
			[['menu_name', 'menu_url'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'menu_name' => 'Menu Name',
			'menu_parent' => 'Menu Parent',
			'menu_url' => 'Menu Url',
			'menu_id' => 'Menu ID',
		];
	}
	
	public function getParent()
	{
		return $this->hasOne(static::className(), ['menu_id'=>'menu_parent']);
	}

	public static function parents()
	{
		$parents = self::find()->where(['menu_parent'=>null])->asArray()->all(); 
		foreach ($parents as $parent) {
			$result[$parent['menu_id']] = $parent['menu_name'];
		}
		return $result;
	}
	
	public static function routes(){
		foreach (\Yii::$app->authManager->getOperations() as $name=>$item) {
			if(strpos($name,'*')===false){
				$result[$name] = $name;
			}
		}
		return $result;
	}
}
