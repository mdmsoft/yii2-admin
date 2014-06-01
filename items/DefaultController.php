<?php

namespace mdm\admin\items;

class DefaultController extends \mdm\admin\components\Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

}
