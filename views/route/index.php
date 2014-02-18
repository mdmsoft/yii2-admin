<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 */
$this->title = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Operations</h1>
<p>
	<?= $new ? Html::a("Generate route ($new)", ['generate'], ['class' => 'btn btn-success']) : ''?> 
	<?= Html::a('Create route', ['create'], ['class' => 'btn btn-success'])?>
</p>


<?php
echo Html::beginForm();
echo GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		//'id' => $type == 1 ? 'new' : 'exists',
		'allModels' => $exists,
			]),
	'columns' => [
		[
			'class' => 'yii\\grid\\CheckboxColumn',
			'checkboxOptions' => function($model) {
				return [
					'value' => ArrayHelper::getValue($model, 'name'),
					'checked' => !ArrayHelper::getValue($model, 'exists', false)
				];
			},
		],
		[
			'class' => 'yii\\grid\\DataColumn',
			'attribute' => 'name',
			'contentOptions' => function ($model) {
				return ArrayHelper::getValue($model, 'exists', true) ? [] : ['style' => 'text-decoration: line-through;'];
			}
		]
	]
]);
echo Html::submitButton('Delete', ['name' => 'Submit', 'class' => 'btn btn-danger']);
echo Html::endForm();
?>
