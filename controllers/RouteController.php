<?php

namespace mdm\admin\controllers;

use mdm\admin\components\AccessHelper;
use yii\rbac\Item;
use yii\helpers\ArrayHelper;
use Yii;

class RouteController extends \mdm\admin\components\Controller
{

	public function actionIndex()
	{
		if (isset($_POST['Submit'])) {
			$this->saveDel($_POST['selection']);
		}
		$routes = AccessHelper::getRoutes();
		
		$operation = array_keys(Yii::$app->authManager->getOperations());

		$new_operation = array_diff($routes, $operation);

		$exists = [];
		foreach ($operation as $route) {
			$exists[$route] = ['type' => Item::TYPE_OPERATION, 'name' => $route, 'exists' => in_array($route, $routes)];
		}
		ArrayHelper::multisort($exists, 'exists');
		return $this->render('index', ['new' => count($new_operation), 'exists' => $exists]);
	}

	public function actionGenerate()
	{
		if (isset($_POST['Submit'])) {
			$this->saveNew($_POST['selection']);
		}
		$routes = AccessHelper::getRoutes();
		
		$operation = array_keys(Yii::$app->authManager->getOperations());

		$new_operation = array_diff($routes, $operation);
		if(isset($_POST['Submit']) && count($new_operation)==0){
			$this->redirect(['index']);
		}
		
		$new = [];
		foreach ($new_operation as $route) {
			$new[$route] = ['type' => Item::TYPE_OPERATION, 'name' => $route];
		}
		
		return $this->render('generate', ['new' => $new]);
	}

	protected function saveNew($selections)
	{
		$authManager = Yii::$app->authManager;
		foreach ($selections as $route) {
			$authManager->createItem($route, Item::TYPE_OPERATION);
		}
		$authManager->save();
	}

	protected function saveDel($selections)
	{
		$authManager = Yii::$app->authManager;
		foreach ($selections as $route) {
			$authManager->removeItem($route);
		}
		$authManager->save();
	}

}
