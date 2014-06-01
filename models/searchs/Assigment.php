<?php

namespace mdm\admin\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AssigmentSearch represents the model behind the search form about Assigment.
 */
class Assigment extends Model
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

    public function search($params, $class, $usernameField)
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
            $query->andWhere(['like', $usernameField, $value]);
        }
        return $dataProvider;
    }
}