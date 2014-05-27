<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$asset = mdm\admin\AdminAsset::register($this);

$menus = [
    ['label' => 'Admin', 'url' => ['/admin/default']],
    ['label' => 'Assigment', 'url' => ['/admin/assigment']],
    ['label' => 'Role', 'url' => ['/admin/role']],
    ['label' => 'Permission', 'url' => ['/admin/permission']],
    ['label' => 'Route', 'url' => ['/admin/route']],
    ['label' => 'Rule', 'url' => ['/admin/rule']],
];
?>
<div class="row">
    <div class="col-lg-3">
        <div class="list-group">
            <?php
            foreach ($menus as $menu) {
                $label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($menu['label']);
                echo Html::a($label, $menu['url'], [
                    'class' => strpos(Yii::$app->controller->route, trim($menu['url'][0], '/')) === 0 ? 'list-group-item active' : 'list-group-item',
                ]);
            }
            ?>
        </div>
    </div>
    <div class="col-lg-9">
        <?= $this->render($view, $params, Yii::$app->controller) ?>
    </div>
</div>
