<?php

namespace mdm\admin\components;

/**
 * Description of AccessDependency
 *
 * @author MDMunir
 */
class AccessDependency extends \yii\caching\GroupDependency
{

    public function init()
    {
        $this->group = md5(__CLASS__);
        parent::init();
    }

    public static function resetDependency($cache = null)
    {
        $cache = $cache ? $cache : \Yii::$app->cache;
        $group = md5(__CLASS__);
        return self::invalidate($cache, $group);
    }
}