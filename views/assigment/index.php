<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

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
	$manager = Yii::$app->authManager;
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\DataColumn',
				'attribute' => 'username',
			],
			[
				'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}'
			],
		],
	]);
	?>

</div>
