<?php

namespace mdm\admin\models;

/**
 * This is the model class for table "tbl_menu".
 *
 * @property string $menu_name
 * @property integer $menu_parent
 * @property string $menu_url
 * @property integer $menu_id
 *
 * @property Menu $menuParent
 * @property Menu[] $menus
 */
class Menu extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%menu}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['menu_name'], 'required'],
			[['menu_parent'], 'integer'],
			[['menu_parent'], 'exist','targetAttribute'=>'menu_id'],
			[['menu_parent'], 'detectLoop'],
			[['menu_name', 'menu_route'], 'string', 'max' => 64]
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
			'menu_route' => 'Menu Route',
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getMenuParent()
	{
		return $this->hasOne(Menu::className(), ['menu_id' => 'menu_parent']);
	}
	
	public function detectLoop()
	{
		if(empty($this->menu_parent)){
			$this->menu_parent = null;
			return;
		}
		if($this->isNewRecord){
			return;
		}
		$id = $this->menu_id;
		$parent = self::find($this->menu_parent);
		do {
			if($id == $parent->menu_id){
				$this->addError('menu_parent', 'Loop detected....');
				return;
			}			
		}while (($parent=$parent->menuParent)!=null);
	}
	
	public static function parents()
	{
		$parents = self::find()->asArray()->all(); 
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
