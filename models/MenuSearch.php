<?php

namespace mdm\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mdm\admin\models\Menu;

/**
 * MenuSearch represents the model behind the search form about `mdm\admin\models\Menu`.
 */
class MenuSearch extends Menu
{
    public function rules()
    {
        return [
            [['menu_name', 'menu_parent', 'menu_route'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Menu::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'menu_name', $this->menu_name])
            ->andFilterWhere(['like', 'menu_parent', $this->menu_parent])
            ->andFilterWhere(['like', 'menu_route', $this->menu_route]);

        return $dataProvider;
    }
}
