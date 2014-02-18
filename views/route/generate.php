<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 */

$this->title = 'Generate Operations';
$this->params['breadcrumbs'][] = ['label' => 'Operation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Generate Operations</h1>

<?php
echo Html::beginForm();
echo GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $new,
			]),
	'columns' => [
		[
			'class' => 'yii\\grid\\CheckboxColumn',
			'checkboxOptions' => function($model){
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
echo Html::submitButton('Append', ['name' => 'Submit','class' => 'btn btn-primary']);
echo Html::endForm();
?>
