<?php

namespace mdm\admin\controllers;

class DefaultController extends \yii\web\Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

}
