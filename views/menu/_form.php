<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mdm\admin\models\Menu;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\Menu $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="menu-form">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'menu_name')->textInput(['maxlength' => 64]) ?>

		<?php
			echo $field = $form->field($model, 'menu_parent')->widget(Select2::classname(),[
				'model'=>$model,
				'attribute'=>'menu_parent',
				'data'=>  Menu::parents(),
				'options'=>ArrayHelper::merge(['prompt'=>'Parent Menu'], $field->inputOptions),
			]);
		?>

		<?php
			echo $field = $form->field($model, 'menu_url')->widget(Select2::classname(),[
				'model'=>$model,
				'attribute'=>'menu_url',
				'data'=>  Menu::routes(),
				'options'=>ArrayHelper::merge(['prompt'=>'Route'], $field->inputOptions),
			]);
		?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
