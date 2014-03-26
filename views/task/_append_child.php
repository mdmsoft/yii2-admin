<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use mdm\admin\models\AppendChild;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AppendChild $model
 * @var ActiveForm $form
 */
?>
<div class="append_child">

	<?php $form = ActiveForm::begin(); ?>
	<div class="form-group">
		<?php
		echo Select2::widget([
			'model' => $model,
			'attribute' => 'children',
			'options' => [
				'class' => 'form-control',
				'placeholder' => 'Select gan ... ',
				'multiple' => true
			],
			'data' => AppendChild::avaliableRoles(),
		]);
		?>
	</div>
	<div class="form-group">
		<?= Html::submitButton('Append', ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div><!-- _append_child -->
