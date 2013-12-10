<?php

namespace mdm\admin\components;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Description of Select2
 *
 * @author MDMunir
 */
class Select2 extends InputWidget
{

	/**
	 * @var array the HTML attributes for the input tag.
	 */
	public $options = [];
	public $data = [];
	public $multiple = false;
	private $_options = [];

	/**
	 * 
	 * @throws InvalidConfigException
	 */
	public function init()
	{
		parent::init();
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
		}
		if($this->multiple){
			$this->options['multiple'] = true;
		}
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		if ($this->hasModel()) {
			echo Html::activeDropDownList($this->model, $this->attribute, $this->data, $this->options);
		} else {
			echo Html::dropDownList($this->name, $this->value, $this->data, $this->options);
		}
		$this->registerClientScript();
	}

	/**
	 * Registers the needed JavaScript.
	 */
	public function registerClientScript()
	{
		$options = $this->getClientOptions();
		$options = empty($options) ? '' : Json::encode($options);
		$id = $this->options['id'];
		$js = "jQuery(\"#{$id}\").select2({$options});";
		$view = $this->getView();
		Select2Asset::register($view);
		$view->registerJs($js);
	}

	/**
	 * @return array the options for the text field
	 */
	protected function getClientOptions()
	{
		if(isset($this->_options['ajax'])){
			$this->_options['ajax']['url'] = Html::url(ArrayHelper::getValue($this->_options['ajax'], 'url', ''));
			
		}
		
		return $this->_options;
	}

	public function __set($name, $value)
	{
		if ($this->canSetProperty($name)) {
			parent::__set($name, $value);
		}
		$this->_options[$name] = $value;
	}

	public function __get($name)
	{
		if ($this->canGetProperty($name)) {
			return parent::__get($name);
		}
		return isset($this->_options[$name]) ? $this->_options[$name] : null;
	}

}