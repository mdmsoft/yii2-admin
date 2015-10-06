<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assignment-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php
    Pjax::begin([
        'enablePushState'=>false,
    ]);
    $columns = array_merge(
        [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => $usernameField,
            ],
        ],
        $extraColumns,
        [
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}'
            ],
        ]
    );
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]);
    Pjax::end();
    ?>

</div>
