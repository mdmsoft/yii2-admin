<?php
namespace mdm\admin\components;
/**
 * Description of Controller
 *
 * @author MDMunir
 */
class Controller extends \yii\web\Controller
{
	//put your code here
	public function render($view, $params = array())
	{
		return parent::render('/layouts/manager', ['view'=>$view,'params'=>$params]);
	}
}