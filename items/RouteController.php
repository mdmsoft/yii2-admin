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
        $manager = Yii::$app->getAuthManager();

        $exists = $existsOptions = $routes = [];
        foreach (AccessHelper::getRoutes() as $route) {
            $routes[$route] = $route;
        }
        $allRoutes = $routes;
        foreach ($manager->getPermissions() as $name => $permission) {
            if ($name[0] !== '/') {
                continue;
            }
            $exists[$name] = $name;
            if (isset($allRoutes[$name])) {
                unset($routes[$name]);
            } else {
                $r = explode('&', $name);
                if (!isset($allRoutes[$r[0]])) {
                    $existsOptions[$name] = ['class' => 'lost'];
                }
            }
        }

        return $this->render('index', ['new' => $routes, 'exists' => $exists, 'existsOptions' => $existsOptions]);
    }

    public function actionCreate()
    {
        $model = new Route;
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($model->validate()) {
                $routes = preg_split('/\s*,\s*/', trim($model->route), -1, PREG_SPLIT_NO_EMPTY);
                $this->saveNew($routes);
                AccessHelper::refeshAuthCache();
                $this->redirect(['index']);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionAssign($action)
    {
        $post = Yii::$app->getRequest()->post();
        $routes = $post['routes'];
        $manager = Yii::$app->getAuthManager();
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
        AccessHelper::refeshAuthCache();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return [$this->actionRouteSearch('new', $post['search_av']),
            $this->actionRouteSearch('exists', $post['search_asgn'])];
    }

    public function actionRouteSearch($target, $term = '', $refresh = '0')
    {
        if ($refresh == '1') {
            AccessHelper::refeshFileCache();
        }
        $result = [];
        $manager = Yii::$app->getAuthManager();

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
                if ($name[0] !== '/') {
                    continue;
                }
                if (empty($term) or strpos($name, $term) !== false) {
                    $result[$name] = $name;
                }

                // extract route part from $name
                $r = explode('&', $name);
                if (empty($r[0]) || !in_array($r[0], $routes)) {
                    $existsOptions[$name] = ['class' => 'lost'];
                }
            }
        }
        $options = $target == 'new' ? [] : ['options' => $existsOptions];
        return Html::renderSelectOptions('', $result, $options);
    }

    private function saveNew($routes)
    {
        $manager = Yii::$app->getAuthManager();
        foreach ($routes as $route) {
            try {
                $r = explode('&', $route);
                $item = $manager->createPermission('/' . trim($route, '/'));
                if (count($r) > 1) {
                    $action = '/' . trim($r[0], '/');
                    if (($itemAction = $manager->getPermission($action)) === null) {
                        $itemAction = $manager->createPermission($action);
                        $manager->add($itemAction);
                    }
                    unset($r[0]);
                    foreach ($r as $part) {
                        $part = explode('=', $part);
                        $item->data['params'][$part[0]] = isset($part[1]) ? $part[1] : '';
                    }
                    AccessHelper::setDefaultRouteRule();
                    $item->ruleName = AccessHelper::ROUTE_RULE_NAME;
                    $manager->add($item);
                    $manager->addChild($item, $itemAction);
                } else {
                    $manager->add($item);
                }
            } catch (Exception $e) {
                
            }
        }
    }
}