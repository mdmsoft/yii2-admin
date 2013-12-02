<?php

use yii\helpers\Html;
use yii\grid\GridView;
use mdm\select2\Select2;

/**
 * @var yii\web\View $this
 * @var mdm\auth\models\AuthItem $model
 */
$this->title = 'Update Auth Item: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-item-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<div class="col-lg-6">
		<?php
		echo $this->render('_form', [
			'model' => $model,
		]);
		?>
	</div>
	<div class="col-lg-6">
		<div class="row">
			Children:<br>
			<?php echo Select2::widget([
				'name'=>'test',
				'data'=>['a'=>'satu','b'=>'dua','c'=>'tiga']
				]); ?>
			<?php
			echo GridView::widget([
				'dataProvider' => new yii\data\ArrayDataProvider(['allModels' => $model->getChildren(),]),
				'columns' => [
					['class' => 'yii\grid\CheckboxColumn'],
					'name',
				]
			]);
			?>
		</div>
		<div class="row">
			Routes:<br>
		</div>
	</div>

</div>
