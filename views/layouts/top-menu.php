<?php

use yii\bootstrap\Nav;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$controller = $this->context;
$menus = $controller->module->menus;
$route = $controller->route;
?>
    <?php
    foreach ($menus as $i => $menu) {
        $menus[$i]['active'] = strpos($route, trim($menu['url'][0], '/')) === 0;
    }
    echo Nav::widget([
        'options' => ['class' => 'nav-pills','id'=>'manager-menu'],
        'items' => $menus,
    ]);
    ?>
    <?= $this->render($view, $params, $controller); ?>
<?php 
$css = <<<CSS
#manager-menu {
    background-color:#F5F5F5;
}
CSS;
$this->registerCss($css);
