<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
/* @var $widget NgView */

?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('rbac-admin', 'Permission')?></h3>
                <div class="box-tools pull-right">
                    <button ng-if="!isEdit" class="btn btn-box-tool" ng-click="clickEdit()"><span class="fa fa-pencil"></span></button>
                    <button ng-if="!isEdit" class="btn btn-box-tool" ng-click="clickDelete()"><span class="fa fa-trash"></span></button>
                    <button ng-if="!!isEdit" class="btn btn-box-tool" ng-click="clickSave()"><span class="fa fa-save"></span></button>
                    <button ng-if="!!isEdit" class="btn btn-box-tool" ng-click="clickCancel()"><span class="fa fa-times-circle-o"></span></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box-body form-horizontal">
                        <div class="form-group" ng-class="{'has-error':modelError.name}">
                            <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Name')?></label>
                            <div class="col-sm-9">
                                <input ng-if="isEdit" class="form-control" ng-model="edit.name">
                                <div ng-if="isEdit && modelError.name" class="help-block">{{modelError.name}}</div>
                                <p ng-if="!isEdit" class="form-control-static" >{{model.name}}</p>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error':modelError.description}">
                            <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Description')?></label>
                            <div class="col-sm-9">
                                <input ng-if="isEdit" class="form-control" ng-model="edit.description">
                                <div ng-if="isEdit && modelError.name" class="help-block">{{modelError.description}}</div>
                                <p ng-if="!isEdit" class="form-control-static">{{model.description}}</p>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error':modelError.ruleName}">
                            <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Rule')?></label>
                            <div class="col-sm-9">
                                <select ng-if="isEdit" class="form-control" ng-model="edit.ruleName"
                                        ng-options="rule for rule in rules">
                                    <option></option>
                                </select>
                                <div ng-if="isEdit && modelError.name" class="help-block">{{modelError.ruleName}}</div>
                                <p ng-if="!isEdit" class="form-control-static">{{model.ruleName}}</p>
                            </div>
                        </div>
                        <div class="form-group" ng-class="{'has-error':modelError.data}">
                            <label class="col-sm-3 control-label"><?= Yii::t('rbac-admin', 'Data')?></label>
                            <div class="col-sm-9">
                                <textarea ng-if="isEdit" class="form-control" ng-model="edit.data"></textarea>
                                <div ng-if="isEdit && modelError.name" class="help-block">{{modelError.data}}</div>
                                <p ng-if="!isEdit" class="form-control-static">{{model.data}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="padding: 30px;">
                    <alert ng-repeat="alert in alerts" type="{{alert.type}}" dismiss-on-timeout="{{alert.time}}"
                           close="closeAlert($index)">{{alert.msg}}</alert>
                        </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('rbac-admin', 'Children')?>:</h3>
                <div class="box-tools pull-right">
                    <div class="has-feedback">
                        <input type="text" class="form-control input-sm"
                               ng-model="q1" placeholder="Search..."
                               ng-change="applyFilter1()">
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="mailbox-controls">
                    <!-- Check all button -->
                    <button class="btn btn-default btn-sm" ng-click="changeAll1()">
                        <i class="fa" ng-class="{'fa-check':all1,'fa-square-o':!all1}"></i>
                    </button>
                    <button class="btn btn-default btn-sm" ng-click="clickRevoke()">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                    <div class="pull-right">
                        {{page1.begin + 1}}-{{page1.end}} of {{page1.total}}
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" ng-click="page1.prev();" ng-class="{disabled:page1.begin <= 0}">
                                <i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-default btn-sm" ng-click="page1.next();" ng-class="{disabled:page1.end >= page1.total}">
                                <i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
                <div table-responsive mailbox-messages>
                    <table class="table table-hover">
                        <tbody>
                            <tr ng-repeat="item in displayed1">
                                <td style="width: 35px;">
                                    <input type="checkbox" ng-model="item.selected">
                                </td>
                                <td ng-if="item.name.charAt(0) != '/'" style="width: 40%;">
                                    <span class="label" ng-class="{'label-danger':item.type == 1,'label-success':item.type == 2}">{{item.name}}</span>
                                </td>
                                <td ng-if="item.name.charAt(0) == '/'" colspan="2"><span class="label label-default">{{item.name}}</span></td>
                                <td ng-if="item.name.charAt(0) != '/'">{{item.description}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('rbac-admin', 'Avaliable')?>:</h3>
                <div class="box-tools pull-right">
                    <div class="has-feedback">
                        <input type="text" class="form-control input-sm"
                               ng-model="q2" placeholder="Search..."
                               ng-change="applyFilter2()">
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="mailbox-controls">
                    <!-- Check all button -->
                    <button class="btn btn-default btn-sm" ng-click="changeAll2()">
                        <i class="fa" ng-class="{'fa-check':all2,'fa-square-o':!all2}"></i>
                    </button>
                    <button class="btn btn-default btn-sm" ng-click="clickAssign()">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <div class="pull-right">
                        {{page2.begin + 1}}-{{page2.end}} of {{page2.total}}
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" ng-click="page2.prev();" ng-class="{disabled:page2.begin <= 0}">
                                <i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-default btn-sm" ng-click="page2.next();" ng-class="{disabled:page2.end >= page2.total}">
                                <i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
                <div table-responsive mailbox-messages>
                    <table class="table table-hover">
                        <tbody>
                            <tr ng-repeat="item in displayed2">
                                <td style="width: 35px;">
                                    <input type="checkbox" ng-model="item.selected">
                                </td>
                                <td ng-if="item.name.charAt(0) != '/'" style="width: 40%;">
                                    <span class="label" ng-class="{'label-danger':item.type == 1,'label-success':item.type == 2}">{{item.name}}</span>
                                </td>
                                <td ng-if="item.name.charAt(0) == '/'" colspan="2"><span class="label label-default">{{item.name}}</span></td>
                                <td ng-if="item.name.charAt(0) != '/'">{{item.description}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>