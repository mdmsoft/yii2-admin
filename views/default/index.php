<?php

use yii\web\View;
use yii\helpers\Markdown;

/* @var $this View */

$this->params['breadcrumbs'][] = 'Readme';
echo Markdown::process($this->render('@mdm/admin/README.md'), 'gfm-comment');
