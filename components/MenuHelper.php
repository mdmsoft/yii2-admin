<?php
namespace mdm\admin\components;

/**
 * Description of MenuHelper
 *
 * @author MDMunir
 */
class MenuHelper
{
	public static function getMenu($modul=false){
		$routes = AccessHelper::getItemsRole();
		\Yii::$app->authManager->getOperations($routes);
	}
	
	
}