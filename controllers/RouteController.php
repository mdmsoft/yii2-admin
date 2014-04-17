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
        $manager = Yii::$app->authManager;

        $exists = $existsOptions = $routes = [];
        foreach (AccessHelper::getRoutes() as $route) {
            $routes[$route] = $route;
        }
        foreach ($manager->getPermissions() as $name => $permission) {
            $exists[$name] = $name;
            if (isset($routes[$name])) {
                unset($routes[$name]);
            } else {
                $existsOptions[$name] = ['class' => 'lost'];
            }
        }

        return $this->render('index', ['new' => $routes, 'exists' => $exists, 'existsOptions' => $existsOptions]);
    }

    public function actionGenerate()
    {
        if (isset($_POST['Submit'])) {
            $this->saveNew($_POST['selection']);
        }
        $routes = AccessHelper::getRoutes();

        $operation = array_keys(Yii::$app->authManager->getOperations());

        $new_operation = array_diff($routes, $operation);
        if (isset($_POST['Submit']) && count($new_operation) == 0) {
            $this->redirect(['index']);
        }

        $new = [];
        foreach ($new_operation as $route) {
            $new[$route] = ['type' => Item::TYPE_OPERATION, 'name' => $route];
        }

        return $this->render('generate', ['new' => $new]);
    }

    public function actionCreate()
    {
        $model = new \mdm\admin\models\Route;

        if ($model->load($_POST)) {
            if ($model->validate()) {
                $routes = explode(',', $model->route);
                $this->saveNew($routes);
                $this->redirect(['index']);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionAssign($action)
    {
        $post = Yii::$app->request->post();
        $routes = $post['routes'];
        $manager = Yii::$app->authManager;
        if ($action == 'assign') {
            foreach ($routes as $route) {
                try {
                    $manager->add($manager->createPermission($route));
                } catch (\Exception $e) {
                    
                }
            }
        } else {
            foreach ($routes as $route) {
                $child = $manager->getPermission($route);
                try {
                    $manager->remove($child);
                } catch (\Exception $e) {
                    
                }
            }
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [$this->actionRoleSearch('new', $post['search_av']),
            $this->actionRoleSearch('exists', $post['search_asgn'])];
    }

    public function actionRoleSearch($target, $term = '')
    {
        $result = [];
        $manager = Yii::$app->authManager;

        $existsOptions = [];
        $exists = array_keys($manager->getPermissions());
        $routes = AccessHelper::getRoutes();
        if ($target == 'new') {
            foreach ($routes as $route) {
                if (in_array($route, $exists)) {
                    continue;
                }
                if (empty($term) or strpos($route, $term) !== false) {
                    $result[$route] = $route;
                }
            }
        } else {
            foreach ($exists as $name) {
                if (empty($term) or strpos($name, $term) !== false) {
                    $result[$name] = $name;
                }
                if (!in_array($name, $routes)) {
                    $existsOptions[$name] = ['class' => 'lost'];
                }
            }
        }
        $options = $target == 'new' ? [] : ['options' => $existsOptions];
        return \yii\helpers\Html::renderSelectOptions('', $result, $options);
    }

}
