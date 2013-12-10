<?php
namespace mdm\admin\models;

use yii\db\ActiveRecord;
use \Yii;
/**
 * Description of Assigment
 *
 * @author MDMunir
 */
class Assigment extends ActiveRecord
{
	//put your code here
	private static $_useridField;
	private static $_usernameField;
	
	public static function tableName()
	{
		$class = Yii::$app->user->identityClass;
		if(is_subclass_of($class, ActiveRecord::className())){
			return $class::tableName();
		}
		return parent::tableName();
	}
	
	public function getUserid(){
		
	}

	public function getRoles(){
		if(self::$_useridField === null){
			$module = Yii::$app->controller->module;
			if($module instanceof \mdm\admin\Module){
				self::$_useridField = $module->useridField;
				self::$_usernameField = $module->usernameField;
			}else{
				self::$_useridField = self::$_usernameField = '';
			}
		}
		if(self::$_useridField != ''){
			
		}
	}
}