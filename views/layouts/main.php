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
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <body class="skin-blue layout-top-nav">
        <div class="wrapper">
            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <?= Html::a(Yii::t('rbac-admin', 'RBAC Manager'), ['default/index'], ['class' => 'navbar-brand']) ?>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                                <i class="fa fa-bars"></i>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse pull-right" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li><?= Html::a(Yii::t('rbac-admin', 'Help'), 'https://github.com/mdmsoft/yii2-admin/blob/3.master/README.md') ?></li>
                                <li><?= Html::a(Yii::t('rbac-admin', 'Application'), Yii::$app->homeUrl) ?></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
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
