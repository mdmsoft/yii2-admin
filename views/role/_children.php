<?php

use yii\helpers\Html;
use mdm\select2\Select2;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
?>
<br>
<div class="form-group col-lg-12">
	<div class="col-lg-10">
		<?php
		echo Select2::widget([
			'name' => $type,
			'value' => $values,
			'options' => ['style' => 'width:98%'],
			'data' => $items,
			'placeholder' => 'Select gan ... ',
			'multiple' => true,
		]);
		?>
	</div>
	<div class="col-lg-2">
		<?= Html::submitButton('Append', ['class' => 'btn btn-primary', 'name' => 'Submit', 'value' => $type . ':append']); ?>
	</div>
</div>
<br><br>
<div class="col-lg-12">
	<?php
	echo GridView::widget([
		'dataProvider' => new ArrayDataProvider(['allModels' => $data,]),
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
			],
			'name',
		],
	]);
	echo Html::submitButton('Delete', [
		'class' => 'btn btn-danger',
		'name' => 'Submit',
		'value' => $type . ':delete',
			//'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
	]);
	?>
</div>
