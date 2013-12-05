<?php

namespace mdm\auth;

use yii\rbac\Item;

/**
 * Description of Module
 *
 * @author MDMunir
 */
class Module extends \yii\base\Module
{

	public $userModel;
	public $useridField='id';
	public $usernameField='username';
	
	public function init()
	{
		parent::init();
//		$this->controllerMap['role'] = ['class' => 'mdm\auth\components\AuthItemController', 'type' => Item::TYPE_ROLE];
//		$this->controllerMap['task'] = ['class' => 'mdm\auth\components\AuthItemController', 'type' => Item::TYPE_TASK];
//		$this->controllerMap['operation'] = ['class' => 'mdm\auth\components\AuthItemController', 'type' => Item::TYPE_OPERATION];
		
		if($this->userModel === null){
			$this->userModel = \Yii::$app->user->identityClass;
		}
	}

}