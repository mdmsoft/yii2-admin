<?php

namespace mdm\admin\controllers;

use mdm\admin\models\Assigment;
use mdm\admin\models\AssigmentSearch;
use mdm\admin\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;
use Yii;

/**
 * AssigmentController implements the CRUD actions for Assigment model.
 */
class AssigmentController extends Controller
{

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

    /**
     * Lists all Assigment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AssigmentSearch;

        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
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
        $authManager = Yii::$app->authManager;
        $avaliable = [];
        foreach ($authManager->getRoles() as $role){
            $avaliable[$role->name] = $role->name;
        }
        $assigned = [];
        foreach ($authManager->getRolesByUser($id) as $role){
            $assigned[$role->name] = $role->name;
        }
        return $this->render('view', ['model'=>$model,'avaliable'=>$avaliable,'assigned'=>$assigned]);
    }

    public function actionAssign($id,$action)
    {
        $post = Yii::$app->request->post();
        $roles = $post['roles'];
        $manager = Yii::$app->authManager;
        if($action=='assign'){
            foreach ($roles as $role) {
                $manager->assign($role, $id);
            }
        }  else {
            foreach ($roles as $role) {
                $manager->revoke($role, $id);
            }
        }
        return $this->actionRoleSearch($post['search'], $id);
    }

    public function actionRoleSearch($term = '', $userId = false)
    {
        if ($userId === false) {
            $roles = \Yii::$app->authManager->getRoles();
        } else {
            $roles = \Yii::$app->authManager->getRolesByUser($userId);
        }
        $result = [];
        foreach (array_keys($roles) as $role) {
            if (empty($term) or strpos($role, $term) !== false) {
                $result[$role] = $role;
            }
        }
        return \yii\helpers\Html::renderSelectOptions('', $result);
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
        if (($model = User::findIdentity($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
