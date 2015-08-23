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
                <ul class="nav navbar-nav">
                    <li><a href="#assignment">Assignment</a></li>
                    <li><a href="#role">Role</a></li>
                    <li><a href="#permission">Permission</a></li>
                    <li><a href="#rule">Rule</a></li>
                    <li><a href="#route">Route</a></li>
                    <li><a href="#menu">Menu</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
