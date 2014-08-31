<?php

namespace mdm\admin\items;

use mdm\admin\models\Assigment;
use mdm\admin\models\searchs\Assigment as AssigmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\helpers\Html;
use mdm\admin\components\MenuHelper;
use yii\web\Response;

/**
 * AssigmentController implements the CRUD actions for Assigment model.
 */
class AssigmentController extends Controller
{
    public $userClassName;
    public $idField = 'id';
    public $usernameField = 'username';
    public $searchClass;

    public function init()
    {
        parent::init();
        if ($this->userClassName === null) {
            $this->userClassName = Yii::$app->getUser()->identityClass;
            $this->userClassName = $this->userClassName ? : 'common\models\User';
        }
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'assign' => ['post'],
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

        if ($this->searchClass === null) {
            $searchModel = new AssigmentSearch;
        } else {
            $class = $this->searchClass;
            $searchModel = new $class;
        }

        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams(), $this->userClassName, $this->usernameField);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'idField' => $this->idField,
                'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * Displays a single Assigment model.
     * @param  integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $authManager = Yii::$app->authManager;
        $avaliable = [];
        foreach ($authManager->getRoles() as $role) {
            $avaliable[$role->name] = $role->name;
        }
        $assigned = [];
        foreach ($authManager->getRolesByUser($id) as $role) {
            $assigned[$role->name] = $role->name;
            unset($avaliable[$role->name]);
        }

        return $this->render('view', [
                'model' => $model,
                'avaliable' => $avaliable,
                'assigned' => $assigned,
                'idField' => $this->idField,
                'usernameField' => $this->usernameField,
        ]);
    }

    public function actionAssign($id, $action)
    {
        $post = Yii::$app->request->post();
        $roles = $post['roles'];
        $manager = Yii::$app->authManager;
        $error = [];
        if ($action == 'assign') {
            foreach ($roles as $role) {
                try {
                    $manager->assign($manager->getRole($role), $id);
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        } else {
            foreach ($roles as $role) {
                try {
                    $manager->revoke($manager->getRole($role), $id);
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [$this->actionRoleSearch($id, 'avaliable', $post['search_av']),
            $this->actionRoleSearch($id, 'assigned', $post['search_asgn']),
            $error];
    }

    public function actionRoleSearch($id, $target, $term = '')
    {
        $authManager = Yii::$app->authManager;
        $avaliable = [];
        foreach ($authManager->getRoles() as $role) {
            $avaliable[$role->name] = $role->name;
        }
        $assigned = [];
        foreach ($authManager->getRolesByUser($id) as $role) {
            $assigned[$role->name] = $role->name;
            unset($avaliable[$role->name]);
        }
        $result = [];
        if (!empty($term)) {
            foreach (${$target} as $role) {
                if (strpos($role, $term) !== false) {
                    $result[$role] = $role;
                }
            }
        } else {
            $result = ${$target};
        }

        return Html::renderSelectOptions('', $result);
    }

    /**
     * Finds the Assigment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer               $id
     * @return Assigment             the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $class = $this->userClassName;
        if (($model = $class::findIdentity($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
