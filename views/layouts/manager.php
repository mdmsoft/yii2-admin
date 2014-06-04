<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
//$asset = mdm\admin\AdminAsset::register($this);
$menus = $this->context->module->menus;
?>
<div class="row">
    <div class="col-lg-3">
        <div id="manager-menu" class="list-group">
            <?php
            foreach ($menus as $menu) {
                $label = '<i class="glyphicon glyphicon-chevron-right pull-right"></i>' . Html::encode($menu['label']);
                echo Html::a($label, $menu['url'], [
                    'class' => strpos(Yii::$app->controller->route, trim($menu['url'][0], '/')) === 0 ? 'list-group-item active' : 'list-group-item',
                ]);
            }
            ?>
        </div>
    </div>
    <div class="col-lg-9">
        <?= $this->render($view, $params, $this->context) ?>
    </div>
</div>
