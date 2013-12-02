<?php

use yii\helpers\Html;
use yii\grid\GridView;
use mdm\auth\models\AuthItem;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var mdm\auth\models\AuthItemSearch $searchModel
 */

$this->title = 'Auth Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create AuthItem', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php 
	$typeNames = AuthItem::getTypeName();
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'name',
			[
				'attribute'=>'type',
				'filter'=>  $typeNames,
				'value'=>function($model) use($typeNames){
					return $typeNames[$model->type];
				}
			],
			'description:ntext',

			[
				'class' => 'yii\grid\ActionColumn',
				'urlCreator' => function($model, $action){
					return Yii::$app->controller->createUrl($action, ['id' => $model->name]);
				}],
		],
	]); ?>

</div>
