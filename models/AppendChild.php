<?php

namespace mdm\auth\models;

use \Yii;

/**
 * Description of AppendChild
 *
 * @author MDMunir
 */
class AppendChild extends \yii\base\Model
{

	//put your code here
	public $id;
	public $children;
	private static $_roles;

	public function rules()
	{
		return [
			[['id', 'children'], 'safe'],
		];
	}

	public function save()
	{
		if (count($this->children)) {
			$item = Yii::$app->authManager->getItem($this->id);
			foreach ($this->children as $child) {
				try {
					$item->addChild($child);
				} catch (\yii\base\Exception $exc) {
					//echo $exc->getTraceAsString();
				}
			}
		}
	}

	public static function avaliableRoles()
	{
		if (self::$_roles === null) {
			self::$_roles = [];
			foreach (Yii::$app->authManager->getItems(null, 2) as $item) {
				self::$_roles[$item->name] = $item->name;
			}
		}
		return self::$_roles;
	}

}