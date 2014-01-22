<?php

namespace mdm\admin\controllers;

class DefaultController extends \mdm\admin\components\Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

}
