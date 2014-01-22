<?php

namespace mdm\admin\controllers;

use mdm\admin\models\Assigment;
use mdm\admin\models\AssigmentSearch;
use mdm\admin\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * AssigmentController implements the CRUD actions for Assigment model.
 */
class AssigmentController extends Controller
{

	private $_userModel;
	private $_useridField;
	private $_usernameField;

	/**
	 *
	 * @var \yii\rbac\Manager
	 */
	private $_authManager;

	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function init()
	{
		parent::init();
		/* @var \mdm\admin\Module $module */
		$module = $this->module;
		$this->_userModel = $module->userModel;
		$this->_useridField = $module->useridField;
		$this->_usernameField = $module->usernameField;
		$this->_authManager = \Yii::$app->authManager;
	}

	/**
	 * Lists all Assigment models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new AssigmentSearch;

		$dataProvider = $searchModel->search($this->_userModel, $this->_usernameField, $_GET);
		return $this->render('index', [
					'dataProvider' => $dataProvider,
					'searchModel' => $searchModel,
					'useridField' => $this->_useridField,
					'usernameField' => $this->_usernameField,
		]);
	}

	/**
	 * Displays a single Assigment model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);
		$values = [];
		if (isset($_POST['Submit'])) {
			$values = $_POST;
			if ($_POST['Submit'] == 'append') {
				foreach (ArrayHelper::getValue($values, 'append', []) as $itemName) {
					$this->_authManager->assign($id, $itemName);
				}
				$this->_authManager->save();
				ArrayHelper::remove($values, 'append');
			} else {
				foreach (ArrayHelper::getValue($values, 'delete', []) as $itemName) {
					$this->_authManager->revoke($id, $itemName);
				}
				$this->_authManager->save();
				ArrayHelper::remove($values, 'delete');
			}
		}
		$assigments = array_keys($this->_authManager->getItems($id));

		return $this->render('view', [
					'model' => $model,
					'useridField' => $this->_useridField,
					'usernameField' => $this->_usernameField,
					'values' => $values,
					'assigments' => $assigments,
		]);
	}

	/**
	 * Finds the Assigment model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Assigment the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		$class = $this->_userModel;
		if (($model = $class::findIdentity($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
