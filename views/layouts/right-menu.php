<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$controller = $this->context;
$menus = $controller->module->menus;
$route = $controller->route;
foreach ($menus as $i => $menu) {
    $menus[$i]['active'] = strpos($route, trim($menu['url'][0], '/')) === 0;
}
$this->params['nav-items'] = $menus;
?>
<?php $this->beginContent($controller->module->mainLayout) ?>
<div class="row">
    <div class="col-sm-9">
        <?= $content ?>
    </div>
    <div class="col-sm-3">
        <div id="manager-menu" class="list-group">
            <?php
            foreach ($menus as $menu) {
                $label = Html::tag('span', Html::encode($menu['label']), []);
                $active = $menu['active'] ? ' active' : '';
                echo Html::a($label, $menu['url'], [
                    'class' => 'list-group-item' . $active,
                ]);
            }
            ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>
