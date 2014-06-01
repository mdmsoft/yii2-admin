<?php

namespace mdm\admin\items;

use mdm\admin\components\AccessHelper;
use Yii;
use mdm\admin\models\Route;
use yii\web\Response;
use yii\helpers\Html;
use Exception;

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
            if ($name[0] !== '/') {
                continue;
            }
            $exists[$name] = $name;
            if (isset($routes[$name])) {
                unset($routes[$name]);
            } else {
                $existsOptions[$name] = ['class' => 'lost'];
            }
        }

        return $this->render('index', ['new' => $routes, 'exists' => $exists, 'existsOptions' => $existsOptions]);
    }

    public function actionCreate()
    {
        $model = new Route;
        if ($model->load(Yii::$app->request->post())) {
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
            $this->saveNew($routes);
        } else {
            foreach ($routes as $route) {
                $child = $manager->getPermission($route);
                try {
                    $manager->remove($child);
                } catch (Exception $e) {
                    
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [$this->actionRouteSearch('new', $post['search_av']),
            $this->actionRouteSearch('exists', $post['search_asgn'])];
    }

    public function actionRouteSearch($target, $term = '')
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
        return Html::renderSelectOptions('', $result, $options);
    }

    private function saveNew($routes)
    {
        $manager = Yii::$app->authManager;
        foreach ($routes as $route) {
            try {
                $manager->add($manager->createPermission('/' . trim($route, ' /')));
            } catch (Exception $e) {
                
            }
        }
    }

}
