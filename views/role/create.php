<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var mdm\auth\models\AuthItem $model
 */

$this->title = 'Create Role';
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
