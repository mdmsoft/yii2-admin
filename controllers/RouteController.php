<?php

namespace mdm\admin\controllers;

use mdm\admin\components\AccessHelper;
use yii\rbac\Item;
use Yii;

class RouteController extends \yii\web\Controller
{

	public $layout = 'manager';
	public function actionIndex()
	{
		if (isset($_POST['Submit'])) {
			if ($_POST['Submit'] == 'New') {
				if (!empty($_POST['selection'])) {
					$this->saveNew($_POST['selection']);
				}
			}  else {
				if (!empty($_POST['selection'])) {
					$this->saveDel($_POST['selection']);
				}
			}
		}
		$routes = AccessHelper::getRoutes();
		$task = array_keys(Yii::$app->authManager->getTasks());
		$operation = array_keys(Yii::$app->authManager->getOperations());

		$new_task = array_diff($routes['task'], $task);
		$new_operation = array_diff($routes['operation'], $operation);

		$new = [];
		foreach ($new_task as $route) {
			$new[$route] = ['type' => Item::TYPE_TASK, 'name' => $route];
		}

		foreach ($new_operation as $route) {
			$new[$route] = ['type' => Item::TYPE_OPERATION, 'name' => $route];
		}

		$exists = [];
		foreach ($task as $route) {
			$exists[$route] = ['type' => Item::TYPE_TASK, 'name' => $route, 'exists' => in_array($route, $routes['task'])];
		}

		foreach ($operation as $route) {
			$exists[$route] = ['type' => Item::TYPE_OPERATION, 'name' => $route, 'exists' => in_array($route, $routes['operation'])];
		}

		return $this->render('index', ['new' => $new, 'exists' => $exists]);
	}

	protected function saveNew($selections)
	{
		$authManager = Yii::$app->authManager;
		foreach ($selections as $route) {
			$authManager->createItem($route, strpos($route, '*') === strlen($route) - 1 ? Item::TYPE_TASK : Item::TYPE_OPERATION);
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
