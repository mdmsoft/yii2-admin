<?php
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$menus = [
	['label'=>'User','url'=>['/admin/user']],
	['label'=>'Role','url'=>['/admin/role']],
	['label'=>'Route','url'=>['/admin/route']],
	['label'=>'Admin','url'=>['/admin/default']],
];
?>
<?php $this->beginContent('@mdm/auth/views/layouts/main.php'); ?>
<div class="row">
	<div class="col-lg-3">
		<div class="list-group">
			<?php
			foreach ($menus as $menu) {
				$label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($menu['label']);
				echo Html::a($label, $menu['url'], [
					'class' => strpos(Yii::$app->controller->route,trim($menu['url'][0],'/'))===0 ? 'list-group-item active' : 'list-group-item',
				]);
			}
			?>
		</div>
	</div>
	<div class="col-lg-9">
		<?= $content ?>
	</div>
</div>
<?php $this->endContent(); ?>
