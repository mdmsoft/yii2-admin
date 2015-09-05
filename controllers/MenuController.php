<?php

namespace mdm\admin\controllers;

use Yii;
use mdm\admin\models\Menu;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use mdm\admin\classes\MenuHelper;
use yii\data\ActiveDataProvider;

/**
 * MenuController implements the CRUD actions for Menu model.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class MenuController extends Controller
{

    protected function verbs()
    {
        return[
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'update' => ['POST', 'PUT'],
            'create' => ['POST'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Menu::find();
        $query->joinWith(['menuParent'=>function($q){
            $q->from(Menu::tableName().' p');
        }]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param  integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function actionValues()
    {
        return[
            'menus'=>  Menu::find()->asArray()->all(),
            'routes'=>Menu::getSavedRoutes(),
        ];
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu;

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            MenuHelper::invalidate();
        }
        return $model;
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            MenuHelper::invalidate();
        }
        return $model;
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        MenuHelper::invalidate();

        return true;
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}