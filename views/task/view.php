<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use mdm\admin\components\AccessHelper;
use mdm\admin\components\Select2;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Update', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
		<?php
		echo Html::a('Delete', ['delete', 'id' => $model->name], [
			'class' => 'btn btn-danger',
			'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
			'data-method' => 'post',
		]);
		?>
	</p>

	<?php
	echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			'name',
			'type',
			'description:ntext',
			'biz_rule:ntext',
			'data:ntext',
		],
	]);
	?>

	<?php $form = ActiveForm::begin(); ?>
	<br>
<div class="form-group col-lg-12">
	<div class="col-lg-10">
		<?php
		echo Select2::widget([
			'name' => 'append',
			'value' => empty($states['append'])?[]:$states['append'],
			'options' => ['style' => 'width:98%'],
			'data' => AccessHelper::getAvaliableChild(Item::TYPE_TASK),
			'placeholder' => 'Select gan ... ',
			'multiple' => true,
		]);
		?>
	</div>
	<div class="col-lg-2">
		<?= Html::submitButton('Append', ['class' => 'btn btn-primary', 'name' => 'Submit', 'value'=>'append']); ?>
	</div>
</div>
<br><br>
<div class="col-lg-12">
	<?php
	$deleted = empty($states['delete'])?[]:$states['delete'];
	echo GridView::widget([
		'dataProvider' => new ArrayDataProvider(['allModels' => $model->getChildren(),]),
		'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
				'name' => 'delete',
				'checkboxOptions' => function($model) use($deleted){
					$name = ArrayHelper::getValue($model, 'name');
					return[
						'value' => $name,
						'checked' => in_array($name, $deleted),
					];
				}
			],
			'name',
		],
	]);
	echo Html::submitButton('Delete', [
		'class' => 'btn btn-danger',
		'name' => 'Submit',
		'value'=>'delete',
		'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
	]);
	?>
</div>
	<?php ActiveForm::end(); ?>
</div>
