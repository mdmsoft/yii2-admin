<?php

namespace mdm\admin\components;

/**
 * Description of AccessDependency
 *
 * @author MDMunir
 */
class AccessDependency extends \yii\caching\Dependency
{
	//put your code here

	const DEPENDENCY_KEY = '_AUTH_DEPENDENCY';

	/**
	 * Generates the data needed to determine if dependency has been changed.
	 * Derived classes should override this method to generate the actual dependency data.
	 * @param \yii\caching\Cache $cache the cache component that is currently evaluating this dependency
	 * @return mixed the data needed to determine if dependency has been changed.
	 */
	protected function generateDependencyData($cache)
	{
		$key = static::DEPENDENCY_KEY;
		$result = $cache->get($key);
		if ($result === false) {
			$result = time();
			$cache->set($key, $result);
		}
		return $result;
	}

	public static function resetDependency($cache = null)
	{
		$cache = $cache === null ? \Yii::$app->cache: $cache;
		if ($cache !== null) {
			$cache->set(static::DEPENDENCY_KEY, time());
		}
	}

}