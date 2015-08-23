<?php

use yii\web\View;
use dee\angular\NgView;

//use yii\helpers\Html;

/* @var $this View */
/* @var $widget NgView */

?>
<div class="box box-solid box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('rbac-admin', 'Menu')?></h3>
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
            <div class="form-group" ng-class="{'has-error':modelError.parent}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Parent')?></label>
                <div class="col-sm-9">
                    <input class="form-control" ng-model="model.menuParent"
                           typeahead="menu as menu.name for menu in menus | filter:$viewValue | limitTo:8">
                    <div ng-if="modelError.parent" class="help-block">{{modelError.parent}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{'has-error':modelError.route}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Route')?></label>
                <div class="col-sm-9">
                    <input class="form-control" ng-model="model.route"
                           typeahead="route for route in routes | filter:$viewValue | limitTo:8">
                    <div ng-if="modelError.route" class="help-block">{{modelError.route}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{'has-error':modelError.data}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Data')?></label>
                <div class="col-sm-9">
                    <textarea class="form-control" ng-model="model.data"></textarea>
                    <div ng-if="modelError.data" class="help-block">{{modelError.data}}</div>
                </div>
            </div>
            <div class="form-group" ng-class="{'has-error':modelError.order}">
                <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Order')?></label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" ng-model="model.order">
                    <div ng-if="modelError.order" class="help-block">{{modelError.order}}</div>
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