<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var mdm\admin\models\AssigmentSearch $searchModel
 */
$this->title = 'Assigments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assigment-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php
    Pjax::begin([
        'enablePushState'=>false,
    ]);
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\DataColumn',
				'attribute' => $usernameField,
			],
			[
				'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}'
			],
		],
	]);
    Pjax::end();
	?>

</div>
