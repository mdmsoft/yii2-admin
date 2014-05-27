<?php

namespace mdm\admin\components;

use yii\web\Application;
use yii\web\ForbiddenHttpException;
use mdm\admin\Module;
use Yii;

/**
 * Description of AccessControl
 *
 * @author MDMunir
 * @property \yii\db\Connection $db Database connection.
 */
class AccessControl extends \yii\base\Behavior
{
    /**
     *
     * @var Module 
     */
    public $module;

    public function __construct(Module $module, $config = [])
    {
        $this->module = $module;
        parent::__construct($config);
    }

    public function events()
    {
        return[
            Application::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * 
     * @param \yii\web\User $user
     * @return array
     */
    public function getUserRoutes($user)
    {
        $key = [__CLASS__, $user->id];
        if (($cache = $this->module->cache) === null || ($result = $cache->get($key)) === false) {
            $result = [];
            foreach (Yii::$app->authManager->getPermissionsByUser($user->id) as $name => $permission) {
                if ($name[0] === '/') {
                    $result[] = substr($name, 1);
                }
            }
            if ($cache !== null) {
                $cache->set($key, $result, 3600, new AccessDependency());
            }
        }
        if ($user->getIsGuest() && is_array($user->loginUrl) && isset($user->loginUrl[0])) {
            $result[] = $user->loginUrl[0];
        }
        $result[] = Yii::$app->errorHandler->errorAction;
        return $result;
    }

    /**
     * 
     * @param \yii\base\ActionEvent $event
     */
    public function beforeAction($event)
    {
        $action = $event->action;
        $actionId = $action->uniqueId;
        if ($this->checkAccessRoutes($this->module->allowActions, $actionId)) {
            return true;
        }
        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return true;
        }
        
        $user = Yii::$app->user;
        if ($this->checkAccessRoutes($this->getUserRoutes($user), $actionId)) {
            return true;
        }
        $this->denyAccess($user);
    }

    protected function checkAccessRoutes($routes, $actionId)
    {
        if (in_array($actionId, $routes)) {
            return true;
        }
        foreach ($routes as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim($route, "*");
                if ($route === '' || strpos($actionId, $route) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param yii\web\User $user the current user
     * @throws yii\web\ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}