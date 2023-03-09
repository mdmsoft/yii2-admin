<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $controller \yii\web\Controller string */


$controller = $this->context;
$menus = $controller->module->menus;
$route = $controller->route;
$user = Yii::$app->getUser();
foreach ($menus as $i => $menu) {
    $menus[$i]['active'] = strpos($route, trim((string)$menu['url'][0], '/')) === 0;
}
$this->params['nav-items'] = $menus;
?>
<?php $this->beginContent($controller->module->mainLayout) ?>
<div class="row">
    <div class="col-sm-3">
        <div id="manager-menu" class="list-group">
            <?php
            foreach ($menus as $menu) {
                $r = \mdm\admin\components\Helper::checkRoute($menu['url'][0]);
                if($r) {
                    $label = Html::tag('i', '', ['class' => 'glyphicon glyphicon-chevron-right pull-right']) .
                        Html::tag('span', Html::encode($menu['label']), []);
                    $active = $menu['active'] ? ' active' : '';
                    echo Html::a($label, $menu['url'], [
                        'class' => 'list-group-item' . $active,
                    ]);
                }
            }
            ?>
        </div>
    </div>
    <div class="col-sm-9">
        <?= $content ?>
    </div>
</div>
<?php
list(, $url) = Yii::$app->assetManager->publish('@mdm/admin/assets');
$this->registerCssFile($url . '/list-item.css');
?>

<?php $this->endContent(); ?>
