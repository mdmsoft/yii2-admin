<?php

namespace mdm\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use mdm\admin\models\Menu;

/**
 * MenuSearch represents the model behind the search form about Menu.
 */
class MenuSearch extends Model
{
	public $menu_name;
	public $menu_parent;
	public $menu_url;
	public $menu_id;

	public function rules()
	{
		return [
			[['menu_name', 'menu_url'], 'safe'],
			[['menu_parent', 'menu_id'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'menu_name' => 'Menu Name',
			'menu_parent' => 'Menu Parent',
			'menu_url' => 'Menu Url',
			'menu_id' => 'Menu ID',
		];
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

		$this->addCondition($query, 'menu_name', true);
		$this->addCondition($query, 'menu_parent');
		$this->addCondition($query, 'menu_url', true);
		$this->addCondition($query, 'menu_id');
		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
