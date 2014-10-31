<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('rbac-admin', 'Generate Routes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Yii::t('rbac-admin', 'Generate Routes') ?></h1>

<?php
echo Html::beginForm();
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $new,
            ]),
    'columns' => [
        [
            'class' => 'yii\\grid\\CheckboxColumn',
            'checkboxOptions' => function ($model) {
                return [
                    'value' => ArrayHelper::getValue($model, 'name'),
                    'checked' => true,
                ];
            },
        ],
        [
            'class' => 'yii\\grid\\DataColumn',
            'attribute' => 'name',
        ]
    ]
]);
echo Html::submitButton(Yii::t('rbac-admin', 'Append'), ['name' => 'Submit','class' => 'btn btn-primary']);
echo Html::endForm();
?>
