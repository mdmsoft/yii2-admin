<?php

namespace mdm\auth\components;

use yii\db\Query;
use Yii;

/**
 * Description of AccessControl
 *
 * @author MDMunir
 */
class AccessControl extends \yii\base\Behavior
{

	public $tableAccess = 'm_access_route';
	public $tableMenu = 'm_menu';

	public function events()
	{
		return[
				//Application::EVENT_BEFORE_ACTION => 'beforeAction'
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
			return;
		}
		$roles = AccessHelper::getItemsRole();
		$names = array_keys($roles);

		$query = new Query;
		$count = $query->from($this->tableAccess)
				->where(['and', 'name' => $names, [
						'or', 'route' => $action->uniqueId, 'route' => $action->controller->uniqueId . '/*'
			]])
				->count();
		if ($count == 0) {
			$this->denyAccess(Yii::$app->user);
		}
	}

	/**
	 * Denies the access of the user.
	 * The default implementation will redirect the user to the login page if he is a guest;
	 * if the user is already logged, a 403 HTTP exception will be thrown.
	 * @param yii\web\User $user the current user
	 * @throws yii\web\HttpException if the user is already logged in.
	 */
	protected function denyAccess($user)
	{
		if ($user->getIsGuest()) {
			$user->loginRequired();
		} else {
			throw new HttpException(403, Yii::t('yii', 'You are not allowed to perform this action.'));
		}
	}

	public function getMenu()
	{
//		$roles = AccessHelper::getItemsRole();
//		$names = array_keys($roles);

		$query = new Query;
		$names = ['c', 'b'];
		$items = $query->distinct()
				->select(['p.id as p_id', 'm.id', 'm.menu', 'm.route', 'm.priority'])
				->from($this->tableMenu . ' m')
				->innerJoin($this->tableAccess . ' a', ['or',
					'[[m.route]] = [[a.route]]',
					"[[m.route]] like concat([[a.route]],'%')"])
				->leftJoin($this->tableMenu . ' p', '[[m.parent]]=[[p.id]]')
				->where(['name' => $names])
				->orderBy('[[p.id]],[[m.priority]]')
				->createCommand()
				->queryAll();
		return $this->buildMenuRecrusive($items);
	}

	protected function buildMenuRecrusive($items, $parent = null)
	{
		$result = $priority = [];
		foreach ($items as $item) {
			if ($item['p_id'] === $parent) {
				$result[] = [
					'label' => $item['menu'],
					'url' => [$item['route']],
					'items' => $this->buildMenuRecrusive($items, $item['id'])
				];
				$priority[] = $item['priority'];
			}
		}
		array_multisort($priority, $result);
		return $result;
	}

}