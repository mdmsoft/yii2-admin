<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 */
$this->title = 'Update Permission: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-item-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php
	echo $this->render('_form', [
		'model' => $model,
	]);
	?>
</div>
