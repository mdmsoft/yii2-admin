<?php

namespace mdm\admin\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;

/**
 * Description of BizRule
 *
 * @author MDMunir
 */
class BizRule extends Model
{
    /**
     * @var string name of the rule
     */
    public $name;

    public function rules()
    {
        return [
            [['name', 'expresion', 'createdAt', 'updatedAt'], 'safe']
        ];
    }

    /**
     * 
     * @param array $params
     * @return \yii\data\ActiveDataProvider|\yii\data\ArrayDataProvider
     */
    public function search($params)
    {
        /* @var \yii\rbac\Manager $authManager */
        $authManager = Yii::$app->authManager;
        $rules = $authManager->getRules();
        if ($this->load($params) && $this->validate() && trim($this->name) !== '') {
            $search = strtolower(trim($this->name));
            $rules = array_filter($rules, function($item) use($search) {
                return (empty($search) || strpos(strtolower($item->name), $search) !== false);
            });
        }
        return new ArrayDataProvider([
            'allModels' => $rules,
        ]);
    }
}