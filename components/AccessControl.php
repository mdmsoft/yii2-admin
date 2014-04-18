<?php

namespace mdm\admin\components;

use yii\web\Application;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * Description of AccessControl
 *
 * @author MDMunir
 * @property \yii\db\Connection $db Database connection.
 */
class AccessControl extends \yii\base\Behavior
{

    public $allowActions = [];

    public function events()
    {
        return[
            Application::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * 
     * @param \yii\base\ActionEvent $event
     */
    public function beforeAction($event)
    {
        $action = $event->action;
        $actionId = $action->uniqueId;
        if ($this->checkAccessRoutes($this->allowActions, $actionId)) {
            return true;
        }
        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return true;
        }
        $user = Yii::$app->user;
        if ($this->checkAccessRoutes(AccessHelper::getUserRoutes($user->getId()), $actionId)) {
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
     * @throws yii\web\AccessDeniedHttpException if the user is already logged in.
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
