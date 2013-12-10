<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var mdm\admin\models\AssigmentSearch $searchModel
 */
$this->title = 'Assigments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assigment-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

	<p>
		<?= Html::a('Create Assigment', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php
	$manager = Yii::$app->authManager;
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\DataColumn',
				'attribute' => 'username',
				'value' => function ($model) use($usernameField) {
					return ArrayHelper::getValue($model, $usernameField);
				}
			],
			[
				'class' => 'yii\grid\DataColumn',
				'label' => 'Roles',
				'value' => function($model) use($manager, $useridField) {
					$roles = array_keys($manager->getRoles($model->{$useridField}));
					if (count($roles) > 5) {
						$roles = array_slice($roles, 0, 5);
						$roles[] = '...';
					} elseif (empty($roles)) {
						$roles = ['&minus;'];
					}
					return Html::a(implode(', ', $roles), ['view', 'id' => $model->{$useridField}]);
				},
				'format' => 'raw',
			],
		],
	]);
	?>

</div>
