<?php /* @var $this yii\base\View */ ?>
<div class="admin-default-index">
	<h1><?= $this->context->action->uniqueId ?></h1>
	<p>
		This is the view content for action "<?= $this->context->action->id ?>".
		The action belongs to the controller "<?= get_class($this->context) ?>"
		in the "<?= $this->context->module->id ?>" module.
	</p>
	<p>
		You may customize this page by editing the following file:<br>
		<code><?= __FILE__ ?></code>
	</p>

	<?php
	if ($this->beginCache('test', ['duration' => 30])) {
		echo date('H:i:s'). '<br/>';
		echo "abcde<br/>";
		echo $this->renderDynamic("return date('H:i:s');");
		$this->endCache();
	}
	?>
</div>
