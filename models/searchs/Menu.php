<?php

namespace mdm\admin\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mdm\admin\models\Menu as MenuModel;

/**
 * Menu represents the model behind the search form about `mdm\admin\models\Menu`.
 */
class Menu extends MenuModel
{

    public function rules()
    {
        return [
            [['menu_id', 'menu_parent'], 'integer'],
            [['menu_name', 'menu_route', 'menu_parent_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = MenuModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $query->leftJoin(['parent' => 'menu'], 'menu.menu_parent=parent.menu_id');
        $dataProvider->getSort()->attributes['menuParent.menu_name'] = [
            'asc' => ['parent.menu_name' => SORT_ASC],
            'desc' => ['parent.menu_name' => SORT_DESC],
            'label' => 'menu_parent',
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'menu_id' => $this->menu_id,
            'menu_parent' => $this->menu_parent,
        ]);

        $query->andFilterWhere(['like', 'menu_name', $this->menu_name])
            ->andFilterWhere(['like', 'menu_route', $this->menu_route])
            ->andFilterWhere(['like', 'parent.menu_name', $this->menu_parent_name]);


        return $dataProvider;
    }
}