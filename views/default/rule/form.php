<?php

use yii\web\View;
use dee\angular\NgView;

//use yii\helpers\Html;

/* @var $this View */
/* @var $widget NgView */

?>
<div class="box box-solid box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('rbac-admin', 'Rule')?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" ng-click="cancel()"><span class="fa fa-remove"></span></button>
        </div>
    </div>
    <div ng-if="!!statusText" style="padding: 30px;">
        <alert type="error" close="closeAlert()" dismiss-on-timeout="3000">{{statusText}}</alert>
    </div>
    <form class="form-horizontal">
        <div class="box-body">
            <div class="form-group" ng-class="{'has-error':modelError.name}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Name')?></label>
                <div class="col-sm-9">
                    <input class="form-control" ng-model="model.name">
                    <div ng-if="modelError.name" class="help-block">{{modelError.name}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{'has-error':modelError.className}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Class Name')?></label>
                <div class="col-sm-9">
                    <input class="form-control" ng-model="model.className">
                    <div ng-if="modelError.className" class="help-block">{{modelError.className}}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <button class="btn btn-primary" ng-click="ok()" type="submit">
                        <span class="fa fa-save"></span></button>
                    <button class="btn btn-danger" ng-click="cancel()">
                        <span class="fa fa-remove"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>