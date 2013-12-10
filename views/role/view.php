<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use mdm\admin\components\AccessHelper;
use yii\bootstrap\Tabs;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
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

	<?php
	$form = ActiveForm::begin();
	echo Tabs::widget([
		'items' => [
			[
				'label' => 'Roles',
				'content' => $this->render('_children', [
					'items' => AccessHelper::getAvaliableRoles(),
					'data' => $model->getRoles(),
					'type' => 'roles',
					'append' => empty($states['append_roles'])?[]:$states['append_roles'],
					'delete' => empty($states['delete_roles'])?[]:$states['delete_roles'],
				]),
				'active' => ($active == 'roles'),
			],
			[
				'label' => 'Routes',
				'content' => $this->render('_children', [
					'items' => AccessHelper::getAvaliableRoutes(),
					'data' => $model->getRoutes(),
					'type' => 'routes',
					'append' => empty($states['append_routes'])?[]:$states['append_routes'],
					'delete' => empty($states['delete_routes'])?[]:$states['delete_routes'],
				]),
				'active' => ($active == 'routes'),
			],
		]
	]);
	ActiveForm::end();
	?>
</div>
