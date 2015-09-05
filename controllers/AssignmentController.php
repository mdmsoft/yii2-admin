<?php

namespace mdm\admin\controllers;

use Yii;
use yii\rest\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use mdm\admin\classes\MenuHelper;
use yii\helpers\ArrayHelper;

/**
 * AssignmentController implements the CRUD actions for Assignment model.
 *
 * @property \mdm\admin\Module $module
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AssignmentController extends Controller
{

    protected function verbs()
    {
        return[
            'index' => ['GET', 'HEAD'],
            'view' => ['GET'],
            'assign' => ['POST'],
            'revoke' => ['POST'],
        ];
    }

    /**
     * Lists all Assignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $manager = Yii::$app->getAuthManager();
        $class = $this->module->userClassName;
        $idField = $this->module->idField;
        $usernameField = $this->module->usernameField;

        $items = array_filter(array_merge($manager->getRoles(), $manager->getPermissions()), function($item) {
            return $item->name[0] !== '/';
        });

        $query = $class::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['like', $this->module->usernameField, Yii::$app->request->get('q', '')]);

        $models = array_map(function($user)use($manager, $idField, $usernameField, $items) {
            $assignment = [];
            foreach ($manager->getAssignments($user[$idField]) as $item) {
                $assignment[] = $items[$item->roleName];
            }
            ArrayHelper::multisort($assignment, ['type', 'name']);
            return[
                'id' => $user->$idField,
                'username' => $user->$usernameField,
                'assignments' => $assignment,
            ];
        }, $dataProvider->getModels());

        $dataProvider->setModels($models);
        return $dataProvider;
    }

    /**
     * Displays a single Assignment model.
     * @param  integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $class = $this->module->userClassName;
        $model = $class::findIdentity($id);
        if($model === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $manager = Yii::$app->getAuthManager();
        $usernameField = $this->module->usernameField;

        $items = array_filter(array_merge($manager->getRoles(), $manager->getPermissions()), function($item) {
            return $item->name[0] !== '/';
        });

        $assignment = [];
        foreach ($manager->getAssignments($id) as $item) {
            $assignment[] = $items[$item->roleName];
            unset($items[$item->roleName]);
        }
        ArrayHelper::multisort($assignment, ['type', 'name']);

        return [
            'id' => $id,
            'username' => $model->$usernameField,
            'assignments' => $assignment,
            'avaliables' => array_values($items),
        ];
    }

    /**
     * Assign or revoke assignment to user
     * @param  integer $id
     * @param  string  $action
     * @return mixed
     */
    public function actionAssign($id)
    {
        $items = Yii::$app->request->post('items', []);
        $manager = Yii::$app->authManager;
        $error = [];
        $count = 0;
        foreach ((array) $items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ? : $manager->getPermission($name);
                $manager->assign($item, $id);
                $count++;
            } catch (\Exception $exc) {
                $error[] = $exc->getMessage();
            }
        }

        MenuHelper::invalidate();
        return[
            'type' => 'S',
            'count' => $count,
            'errors' => $error,
        ];
    }

    /**
     * Assign or revoke assignment to user
     * @param  integer $id
     * @param  string  $action
     * @return mixed
     */
    public function actionRevoke($id)
    {
        $items = Yii::$app->request->post('items', []);
        $manager = Yii::$app->authManager;
        $error = [];
        $count = 0;
        foreach ($items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ? : $manager->getPermission($name);
                $manager->revoke($item, $id);
                $count++;
            } catch (\Exception $exc) {
                $error[] = $exc->getMessage();
            }
        }

        MenuHelper::invalidate();
        return[
            'type' => 'S',
            'count' => $count,
            'errors' => $error,
        ];
    }
}