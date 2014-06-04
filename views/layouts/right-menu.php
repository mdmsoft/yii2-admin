<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$controller = $this->context;
$menus = $controller->module->menus;
$route = $controller->route;
?>
<div class="row">
    <div class="col-lg-9">
        <?= $this->render($view, $params, $controller) ?>
    </div>
    <div class="col-lg-3">
        <div id="manager-menu" class="list-group">
            <?php
            foreach ($menus as $menu) {
                $label = Html::tag('span', Html::encode($menu['label']), []);
                $active = strpos($route, trim($menu['url'][0], '/')) === 0 ? ' active' : '';
                echo Html::a($label, $menu['url'], [
                    'class' => 'list-group-item' . $active,
                ]);
            }
            ?>
        </div>
    </div>
</div>
