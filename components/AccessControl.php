<?php

namespace mdm\admin\components;

use yii\web\Application;
use yii\web\AccessDeniedHttpException;
use Yii;

/**
 * Description of AccessControl
 *
 * @author MDMunir
 * @property \yii\db\Connection $db Database connection.
 */
class AccessControl extends \yii\base\Behavior
{

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
		if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
			return true;
		}
		$user = Yii::$app->user;
		$route = $action->uniqueId;
		if ($user->checkAccess($route)) {
			return;
		}

		if ($user->checkAccess($action->controller->uniqueId . '/*')) {
			return;
		}
		$module = $action->controller->module;
		while ($module !== null) {
			$id = $module->uniqueId;
			if ($user->checkAccess($id === '' ? '*' : $id . '/*')) {
				return;
			}
			$module = $module->module;
		}
		$this->denyAccess($user);
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
