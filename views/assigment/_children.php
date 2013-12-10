<?php

use yii\helpers\Html;
use mdm\widgets\Select2;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use mdm\admin\components\AccessHelper;
?>
<br>
<?= Html::beginForm() ?>
<div class="form-group col-lg-12">
	<div class="col-lg-10">
		<?php
		echo Select2::widget([
			'name' => 'append',
			'value' => $append,
			'options' => ['style' => 'width:98%'],
			'data' => AccessHelper::getAvaliableRoles(),
			'multiple' => true,
		]);
		?>
	</div>
	<div class="col-lg-2">
		<?= Html::submitButton('Append', ['class' => 'btn btn-primary', 'name' => 'Submit', 'value' => 'append']); ?>
	</div>
</div>
<br><br>
<div class="col-lg-12">
	<?php
	echo GridView::widget([
		'dataProvider' => new ArrayDataProvider(['allModels' => $assigments,]),
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'name' => 'delete',
				'checkboxOptions' => function($model) use($delete) {
					return[
						'value' => $model,
						'checked' => in_array($model, $delete),
					];
				},
			],
			[
				'class'=>'yii\grid\DataColumn',
				'label'=>'Roles',
				'value'=>function($model){
					return $model;
				}
			],
		],
	]);
	echo Html::submitButton('Delete', [
		'class' => 'btn btn-danger',
		'name' => 'Submit',
		'value' => 'delete',
	]);
	?>
</div>
<?= Html::endForm() ?>