<?php
/**
 * @var yii\web\View $this
 */
?>
<h1>route/index</h1>

<p>
	You may change the content of this page by modifying
	the file <code><?php echo __FILE__; ?></code>.
<pre>
	<?php
	$routes = mdm\auth\components\AccessHelper::getRoutes();
	print_r($routes);
	?>
</pre>
</p>
