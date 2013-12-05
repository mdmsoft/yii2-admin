<?php

namespace mdm\auth\controllers;

use mdm\auth\models\AuthItem;
use mdm\auth\models\AuthItemSearch;
use mdm\auth\models\AppendChild;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class RoleController extends Controller
{

	public $layout = 'manager';

	/**
	 *
	 * @var \yii\rbac\Manager;
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
		$this->_authManager = \Yii::$app->authManager;
	}

	/**
	 * Lists all AuthItem models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new AuthItemSearch(['type' => Item::TYPE_ROLE]);
		$dataProvider = $searchModel->search($_GET);

		return $this->render('index', [
					'dataProvider' => $dataProvider,
					'searchModel' => $searchModel,
		]);
	}

	/**
	 * Displays a single AuthItem model.
	 * @param string $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$_map = ['roles' => 'routes', 'routes' => 'roles'];
		$values = ['roles' => [], 'routes' => []];
		$active = 'roles';
		if (isset($_POST['Submit'])) {
			list($type, $action) = explode(':', $_POST['Submit']);
			$values[$_map[$type]] = ArrayHelper::getValue($_POST, $_map[$type], []);
			$active = $type;
		}
		return $this->render('view', [
					'model' => $this->findModel($id),
					'values' => $values,
					'active' => $active,
		]);
	}

	/**
	 * Creates a new AuthItem model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new AuthItem(null);
		$model->type = Item::TYPE_ROLE;
		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->name]);
		} else {
			return $this->render('create', [
						'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing AuthItem model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		$appendModel = new AppendChild;

		if ($appendModel->load($_POST)) {
			$appendModel->id = $id;
			$appendModel->save();
		}

		if ($model->load($_POST)) {
			//$model->save();
		}
		return $this->render('update', [
					'model' => $model,
					'appendModel' => $appendModel,
		]);
	}

	/**
	 * Deletes an existing AuthItem model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->_authManager->removeItem($id);
		return $this->redirect(['index']);
	}

	/**
	 * Finds the AuthItem model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return AuthItem the loaded model
	 * @throws HttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = AuthItem::find($id)) !== null) {
			return $model;
		} else {
			throw new HttpException(404, 'The requested page does not exist.');
		}
	}

}
