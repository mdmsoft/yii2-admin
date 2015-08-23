<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
?>
<?=
NgView::widget([
    'name' => 'dAdmin',
    'requires' => ['ui.bootstrap', 'ngResource', 'dee.angular'],
    'routes' => [
        '/' => [
            'redirectTo' => '/assignment',
        ],
        '/assignment' => [
            'view' => 'assignment/index',
            'js' => 'assignment/index.js',
            'injection' => ['Assignment'],
        ],
        '/assignment/:id' => [
            'view' => 'assignment/view',
            'js' => 'assignment/view.js',
            'injection' => ['Assignment'],
        ],
        //
        '/role' => [
            'view' => 'role/index',
            'js' => 'role/index.js',
            'injection' => ['Item'],
        ],
        '/role/:id*' => [
            'view' => 'role/view',
            'js' => 'role/view.js',
            'injection' => ['Item'],
        ],
        '/role/form' => [
            'show' => false,
            'view' => 'role/form',
            'js' => 'role/form.js',
            'injection' => ['$modalInstance', 'type', 'Item', 'Rule']
        ],
        '/permission' => [
            'view' => 'permission/index',
            'js' => 'permission/index.js',
            'injection' => ['Item'],
        ],
        '/permission/:id*' => [
            'view' => 'permission/view',
            'js' => 'permission/view.js',
            'injection' => ['Item'],
        ],
        '/rule' => [
            'view' => 'rule/index',
            'js' => 'rule/index.js',
            'injection' => ['Rule'],
        ],
        '/rule/form' => [
            'show' => false,
            'view' => 'rule/form',
            'js' => 'rule/form.js',
            'injection' => ['$modalInstance', 'item', 'Rule']
        ],
        '/rule/view' => [
            'show' => false,
            'view' => 'rule/view',
            'js' => 'rule/view.js',
            'injection' => ['$modalInstance', 'name', 'Rule']
        ],
        '/route' => [
            'view' => 'route/index',
            'js' => 'route/index.js',
            'injection' => ['Route'],
        ],
        '/route/form' => [
            'show' => false,
            'view' => 'route/form',
            'js' => 'route/form.js',
            'injection' => ['$modalInstance', 'Route']
        ],
        '/menu' => [
            'view' => 'menu/index',
            'js' => 'menu/index.js',
            'injection' => ['Menu'],
        ],
        '/menu/form' => [
            'show' => false,
            'view' => 'menu/form',
            'js' => 'menu/form.js',
            'injection' => ['$modalInstance', 'Menu', 'model']
        ],
        '/error/404' => [
            'view' => 'error/404',
        ],
        'otherwise' => [
            'link' => '/error/404',
        ],
    ],
    'js' => 'index.js',
    'useNgApp' => false,
]);
?>
<?php
$url = yii\helpers\Url::canonical();
$url = yii\helpers\Json::htmlEncode(rtrim($url, '/') . '/');
$js = <<<JS
    dAdmin.prefixUrl = {$url};
        
JS;
$this->registerJs($js, \yii\web\View::POS_END);

$css = <<<CSS
.box-solid .form-control-feedback{
    color: #444;
}
CSS;
$this->registerCss($css);
