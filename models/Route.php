<?php
namespace mdm\admin\models;
/**
 * Route
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Route extends \yii\base\Model
{
    public $route;

    public function rules()
    {
        return[
            [['route'],'safe'],
        ];
    }
}
