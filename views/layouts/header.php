<?php

use yii\web\View;

/* @var $this View */
?>
<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand"><?= Yii::$app->name ?></a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav" ng-controller="HeaderMenuCtrl">
                    <li ng-repeat="menu in headerMenu" ng-class="{active:location.path().indexOf(menu.id)===0}">
                        <a ng-href="{{menu.url}}" ng-bind="menu.label"></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
