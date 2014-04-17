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
    public function execute($item, $params)
    {
        if($this->expresion){
            return eval('return '.  trim($this->expresion).';');
        }
        return true;
    }
}
