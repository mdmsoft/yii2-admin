<?php

namespace mdm\auth\components;

/**
 * Description of AccessDependency
 *
 * @author MDMunir
 */
class AccessDependency extends \yii\caching\Dependency
{
	//put your code here

	const KEY_DEPENDENCY = 'MDM-AUTH-CACHE';

	public $for = 'role';

	public function __construct($for, $config = array())
	{
		$this->for = $for;
		parent::__construct($config);
	}

	/**
	 * Generates the data needed to determine if dependency has been changed.
	 * Derived classes should override this method to generate the actual dependency data.
	 * @param \yii\caching\Cache $cache the cache component that is currently evaluating this dependency
	 * @return mixed the data needed to determine if dependency has been changed.
	 */
	protected function generateDependencyData($cache)
	{
		$key = [static::KEY_DEPENDENCY, $this->for];
		$result = $cache->get($key);
		if ($result === false) {
			$result = time();
			$cache->set($key, $result);
		}
		return $result;
	}

	public static function resetDependency($for, $cache = null)
	{
		$cache = $cache === null ? \Yii::$app->getCache() : $cache;
		if ($cache !== null) {
			$cache->set([static::KEY_DEPENDENCY, $for], time());
		}
	}

}