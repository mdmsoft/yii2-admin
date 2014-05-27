<?php
namespace mdm\admin\components;
/**
 * Description of Controller
 *
 * @author MDMunir
 * 
 * @property \mdm\admin\Module $module
 */
class Controller extends \yii\web\Controller
{
	//put your code here
	public function render($view, $params = array())
	{
		return parent::render('/layouts/manager', ['view'=>$view,'params'=>$params]);
	}
}