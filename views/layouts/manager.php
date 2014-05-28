<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$asset = mdm\admin\AdminAsset::register($this);

$moduleId = '/' . trim($this->context->module->uniqueId, '/');

$menus = [
    ['label' => 'Admin', 'url' => [$moduleId . '/default']],
    ['label' => 'Assigment', 'url' => [$moduleId . '/assigment']],
    ['label' => 'Role', 'url' => [$moduleId . '/role']],
    ['label' => 'Permission', 'url' => [$moduleId . '/permission']],
    ['label' => 'Route', 'url' => [$moduleId . '/route']],
    ['label' => 'Rule', 'url' => [$moduleId . '/rule']],
];
?>
<div class="row">
    <div class="col-lg-3">
        <div id="manager-menu" class="list-group">
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
