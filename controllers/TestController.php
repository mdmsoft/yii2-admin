<?php

namespace mdm\auth\controllers;
use yii\helpers\Console;
/**
 * Description of TestController
 *
 * @author MDMunir
 */
class TestController extends \yii\console\Controller
{

	//put your code here
	public function actionIndex()
	{
		echo $this->uniqueId . " : test\n";
//		for($i=1;$i<=100;$i++){
//			sleep(1);
//			Console::showProgress($i, 100);
//		}
		Console::select('isi ',[1,2,3,4]);
	}

	/**
	 * 
	 * @param string $message Message yang akan ditulis di console.
	 */
	public function actionTerserah($message)
	{
		echo $message;
		$this->select($message, $options);
	}

}