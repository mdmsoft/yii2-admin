<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = Yii::t('rbac-admin', 'Routes');
$this->params['breadcrumbs'][] = $this->title;

$opts = Json::htmlEncode([
        'newUrl' => Url::to(['create']),
        'assignUrl' => Url::to(['assign']),
        'refreshUrl' => Url::to(['refresh']),
        'routes' => $routes
    ]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-sm-11">
        <div class="input-group">
            <input id="inp-route" type="text" class="form-control"
                   placeholder="<?= Yii::t('rbac-admin', 'New route(s)') ?>">
            <span class="input-group-btn">
                <a id="btn-new" class="btn btn-success" type="button">
                    <?= Yii::t('rbac-admin', 'Add') ?>
                    <span class="glyphicon glyphicon-s"></span>
                </a>
            </span>
        </div>
    </div>
</div>
<p>&nbsp;</p>
<div class="row">
    <div class="col-sm-5">
        <div class="input-group">
            <input class="form-control search" data-target="avaliable"
                   placeholder="<?= Yii::t('rbac-admin', 'Search for avaliable') ?>">
            <span class="input-group-btn">
                <button id="btn-refresh" class="btn btn-default">
                    <span class="glyphicon glyphicon-refresh"></span>
                </button>>
            </span>
        </div>
        <select multiple size="20" class="form-control list" data-target="avaliable">
        </select>
    </div>
    <div class="col-sm-1">
        <br><br>
        <a href="#" class="btn btn-success btn-assign" data-action="assign">&gt;&gt;</a><br>
        <a href="#" class="btn btn-danger btn-assign" data-action="remove">&lt;&lt;</a>
    </div>
    <div class="col-sm-5">
        <input class="form-control search" data-target="assigned"
               placeholder="<?= Yii::t('rbac-admin', 'Search for assigned') ?>">
        <select multiple size="20" class="form-control list" data-target="assigned">
        </select>
    </div>
</div>
