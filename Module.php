<?php

namespace mdm\admin;

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
	public $appLayout = '@app/views/layouts/main.php';
	
	public function init()
	{
		parent::init();
		if($this->userModel === null && \Yii::$app instanceof \yii\web\Application){
			$this->userModel = \Yii::$app->user->identityClass;
		}
	}

}