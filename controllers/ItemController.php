<?php

namespace mdm\admin\controllers;

use Yii;
use mdm\admin\models\AuthItem;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\rbac\Item;
use mdm\admin\classes\MenuHelper;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ItemController extends Controller
{

    protected function verbs()
    {
        return[
            'index'=>['GET', 'HEAD'],
            'view'=>['GET', 'HEAD'],
            'create'=>['POST'],
            'update'=>['PUT'],
            'delete'=>['DELETE'],
            'add-child'=>['POST'],
            'remove-child'=>['POST']
        ];
    }
    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $manager = Yii::$app->getAuthManager();
        $type = Yii::$app->getRequest()->getQueryParam('type');
        if ($type == Item::TYPE_ROLE) {
            $items = $manager->getRoles();
        } elseif ($type == Item::TYPE_PERMISSION) {
            $items = $manager->getPermissions();
        } else {
            $items = array_merge($manager->getRoles(), $manager->getPermissions());
        }
        return array_values(array_filter($items, function ($item) {
                return $item->name[0] !== '/';
            }));
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem(null);
        if ($model->load(Yii::$app->getRequest()->post(), '') && $model->save()) {
            MenuHelper::invalidate();
        }
        return $model;
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->getRequest()->post(), '') && $model->save()) {
            MenuHelper::invalidate();
        }
        return $model;
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->getAuthManager()->remove($model->item);
        MenuHelper::invalidate();
        return true;
    }

    /**
     * Assign or remove items
     * @param string $id
     * @param string $action
     * @return array
     */
    public function actionAddChild($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $manager = Yii::$app->getAuthManager();
        $parent = $this->findModel($id);
        $error = [];
        $count = 0;
        foreach ((array) $items as $item) {
            $child = $manager->getRole($item);
            $child = $child ? : $manager->getPermission($item);
            try {
                $manager->addChild($parent, $child);
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
     * Assign or remove items
     * @param string $id
     * @param string $action
     * @return array
     */
    public function actionRemoveChild($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $manager = Yii::$app->getAuthManager();
        $parent = $this->findModel($id);
        $error = [];
        $count = 0;
        foreach ((array) $items as $item) {
            $child = $manager->getRole($item);
            $child = $child ? : $manager->getPermission($item);
            try {
                $manager->removeChild($parent, $child);
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
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  string        $id
     * @return AuthItem      the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $manager = Yii::$app->getAuthManager();
        $item = $manager->getRole($id);
        $item = $item ? : $manager->getPermission($id);
        if ($item) {
            return new AuthItem($item);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}