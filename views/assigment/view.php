<?php

use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 * @var mdm\admin\models\Assigment $model
 */

$this->title = ArrayHelper::getValue($model, $usernameField);
$this->params['breadcrumbs'][] = ['label' => 'Assigments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assigment-view">

	<h1>Assigment User <b><?= ArrayHelper::getValue($model, $usernameField) ?></b></h1>

	<?php echo $this->render('_children',[
		'assigments'=>$assigments,
		'append'=>  ArrayHelper::getValue($values, 'append',[]),
		'delete'=>  ArrayHelper::getValue($values, 'delete',[]),
	]); ?>
</div>
