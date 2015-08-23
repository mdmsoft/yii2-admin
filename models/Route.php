<?php

namespace mdm\admin\models;

use yii\base\Model;

/**
 * Route
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Route extends Model
{
    /**
     * @var string Route value. 
     */
    public $name;

    public $data;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            [['name'], 'required'],
        ];
    }

    public function save()
    {
        
    }
}