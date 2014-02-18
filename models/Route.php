<?php
namespace mdm\admin\models;
/**
 * Description of Route
 *
 * @author MDMunir
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