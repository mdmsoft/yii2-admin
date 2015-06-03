<?php

namespace mdm\admin;

/**
 * AdminAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AdminAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@mdm/admin/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'main.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'yii.admin.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
