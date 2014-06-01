<?php
namespace mdm\admin;
/**
 * Description of AdminAsset
 *
 * @author MDMunir
 */
class AdminAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@mdm/admin/assets';
	/**
	 * @inheritdoc
	 */
	public $css = [
		'main.css',
	];
}
