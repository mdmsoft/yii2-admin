<?php

use yii\helpers\Html;
use mdm\admin\models\AuthItem;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="auth-item-form">

	<?php $form = ActiveForm::begin(); ?>


	<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

	<?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

	<?= $form->field($model, 'biz_rule')->textInput() ?>

	<?= $form->field($model, 'data')->textarea(['rows' => 2]) ?>

	<div class="form-group">
		<?php
		echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
			'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
			'name' => 'Submit',
			'value' => 'update',
		])
		?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
