<?php

use yii\helpers\Html;
use dee\adminlte\AdminlteAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AdminlteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode(Yii::t('rbac-admin', 'RBAC Manager')) ?></title>
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <body class="skin-blue layout-top-nav" ng-app="dAdmin">
        <div class="wrapper">
            <?= $this->render('header'); ?>
            <div class="content-wrapper" style="min-height: 443px">
                <div class="container">
                    <section class="content">
                        <?= $content ?>
                    </section>
                </div>
            </div>

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    Version 3.0
                </div>
                <strong>Copyright &copy; 2015 <a href="#">MDMSoft</a>.</strong> All rights reserved.
            </footer>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
