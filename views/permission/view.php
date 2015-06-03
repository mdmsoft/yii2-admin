<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use mdm\admin\AdminAsset;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?php
        echo Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
            'data-method' => 'post',
        ]);
        ?>
    </p>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
            'ruleName',
            'data:ntext',
        ],
    ]);
    ?>
    <div class="row">
        <div class="col-lg-5">
            <?= Yii::t('rbac-admin', 'Avaliable') ?>:
            <input id="search-avaliable"><br>
            <select id="list-avaliable" multiple size="20" style="width: 100%">
            </select>
        </div>
        <div class="col-lg-1">
            <br><br>
            <a href="#" id="btn-add" class="btn btn-success">&gt;&gt;</a><br>
            <a href="#" id="btn-remove" class="btn btn-danger">&lt;&lt;</a>
        </div>
        <div class="col-lg-5">
            <?= Yii::t('rbac-admin', 'Assigned') ?>:
            <input id="search-assigned"><br>
            <select id="list-assigned" multiple size="20" style="width: 100%">
            </select>
        </div>
    </div>

</div>
<?php
AdminAsset::register($this);
$properties = Json::htmlEncode([
        'roleName' => $model->name,
        'assignUrl' => Url::to(['assign']),
        'searchUrl' => Url::to(['search']),
    ]);
$js = <<<JS
yii.admin.initProperties({$properties});

$('#search-avaliable').keydown(function () {
    yii.admin.searchRole('avaliable');
});
$('#search-assigned').keydown(function () {
    yii.admin.searchRole('assigned');
});
$('#btn-add').click(function () {
    yii.admin.addChild('assign');
    return false;
});
$('#btn-remove').click(function () {
    yii.admin.addChild('remove');
    return false;
});

yii.admin.searchRole('avaliable', true);
yii.admin.searchRole('assigned', true);
JS;
$this->registerJs($js);

