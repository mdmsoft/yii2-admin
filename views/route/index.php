<?php

use yii\bootstrap\Tabs;

/**
 * @var yii\web\View $this
 */
?>
<h1>Routes</h1>

<?php
if (count($new)) {
	echo Tabs::widget([
		'items' => [
			[
				'label' => 'New Route',
				'content' => $this->render('_routes', ['data' => $new,'type'=>1]),
				'active' => true
				],
			[
				'label' => 'Existing',
				'content' => $this->render('_routes', ['data' => $exists,'type'=>2])
				],
		]
	]);
} else {
	echo $this->render('_routes', ['data' => $exists,'type'=>2]);
}
?>
