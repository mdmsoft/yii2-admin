<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\base\View $this
 * @var mdm\auth\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-form">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'username')->textInput(['maxlength' => 128]) ?>

		<?= $form->field($model, 'password')->passwordInput(['maxlength' => 128]) ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
