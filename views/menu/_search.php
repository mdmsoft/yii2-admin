<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\MenuSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="menu-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

		<?= $form->field($model, 'menu_name') ?>

		<?= $form->field($model, 'menu_parent') ?>

		<?= $form->field($model, 'menu_url') ?>

		<?= $form->field($model, 'menu_id') ?>

		<div class="form-group">
			<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
