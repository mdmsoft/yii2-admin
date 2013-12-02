<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\base\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var mdm\auth\models\UserSearch $searchModel
 */
$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

	<p>
		<?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => 'yii\grid\SerialColumn',
				'headerOptions' => ['width' => '24px']
			],
			[
				'class' => 'yii\grid\DataColumn',
				'attribute'=>'username',
				'headerOptions' => ['width' => '35%']
			], [
				'class' => 'yii\grid\DataColumn',
				'attribute'=>'roles',
				'headerOptions' => []
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view} {delete}',
				'headerOptions' => ['width' => '64px']
				],
		],
	]);
	?>

</div>
