<?php

namespace mdm\admin\components;

/**
 * Description of RuleRoute
 *
 * @author MDMunir
 */
class BizRule extends \yii\rbac\Rule
{
    public $expresion;

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return $this->expresion === '' || $this->expresion === null || @eval($this->expresion) != 0;
    }
}