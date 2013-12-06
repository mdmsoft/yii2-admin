<?php

namespace mdm\admin\components;

use yii\db\Query;
use yii\db\Connection;
use yii\web\Application;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Description of AccessControl
 *
 * @author MDMunir
 * @property \yii\db\Connection $db Database connection.
 */
class AccessControl extends \yii\base\Behavior
{

	public $tableAccess = 'm_access_route';
	public $tableMenu = 'm_menu';
	public $db = 'db';
	private static $_routeMenuLike = [
		'mysql' => "[[m.route]] like concat([[a.route]],'%')",
	];
	private static $_routeActionLike = [
		'mysql' => ":action_id like concat([[route]],'%')",
	];

	public function events()
	{
		return[
			Application::EVENT_BEFORE_ACTION => 'beforeAction'
		];
	}

	public function init()
	{
		if (is_string($this->db)) {
			$this->db = Yii::$app->getComponent($this->db);
		}
		if (!$this->db instanceof Connection) {
			throw new InvalidConfigException("DbManager::db must be either a DB connection instance or the application component ID of a DB connection.");
		}
		parent::init();
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
		return;
		
		$names = AccessHelper::getItemsRole();
		$query = new Query;
		$count = $query->from($this->tableAccess)
				->where(['name' => $names])
				->andWhere(static::$_routeActionLike[$this->db->driverName], [':action_id' => $action->uniqueId])
				->count('*',$this->db);
//		if ($count == 0) {
//			$this->denyAccess(Yii::$app->user);
//		}
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
		$names = AccessHelper::getItemsRole();
		$driver = $this->db->driverName;
		$query = new Query;
		$items = $query->distinct()
				->select(['p.id as p_id', 'm.id', 'm.menu', 'm.route', 'm.priority'])
				->from($this->tableMenu . ' m')
				->innerJoin($this->tableAccess . ' a', static::$_routeMenuLike[$driver])
				->leftJoin($this->tableMenu . ' p', '[[m.parent]]=[[p.id]]')
				->where(['name' => $names])
				->orderBy('[[p.id]],[[m.priority]]')
				->createCommand($this->db)
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