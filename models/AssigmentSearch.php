<?php

namespace mdm\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AssigmentSearch represents the model behind the search form about Assigment.
 */
class AssigmentSearch extends Model
{

	public $id;
	public $username;

	public function rules()
	{
		return [
			[['id', 'username'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'username' => 'Username',
		];
	}

	public function search($class, $usernameField, $params)
	{
		$query = $class::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$value = $this->username;
		if (trim($value) !== '') {
			$value = '%' . strtr($value, ['%' => '\%', '_' => '\_', '\\' => '\\\\']) . '%';
			$query->andWhere(['like', $usernameField, $value]);
		}
		return $dataProvider;
	}

}
