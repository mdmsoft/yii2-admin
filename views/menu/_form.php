<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mdm\admin\models\Menu;
use mdm\admin\components\Select2;
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
			$field = $form->field($model, 'menu_parent');
			$field->parts['{input}'] = Select2::widget([
				'model'=>$model,
				'attribute'=>'menu_parent',
				'data'=>  Menu::parents(),
				'options'=>ArrayHelper::merge(['prompt'=>'Parent Menu'], $field->inputOptions),
			]);
			echo $field; 
		?>

		<?php
			$field = $form->field($model, 'menu_url');
			$field->parts['{input}'] = Select2::widget([
				'model'=>$model,
				'attribute'=>'menu_url',
				'data'=>  Menu::routes(),
				'options'=>ArrayHelper::merge(['prompt'=>'Route'], $field->inputOptions),
			]);
			echo $field; 
		?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
