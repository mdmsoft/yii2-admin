<?php

namespace mdm\auth\models;

use Yii;
use yii\rbac\Item;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $biz_rule
 * @property string $data
 *
 * @property AuthAssignment $authAssignment
 * @property User[] $users
 * @property AuthItem[] $authItemChildren
 * @property AuthItem[] $authItemParents
 */
class AuthItem extends \yii\base\Model
{

	public $name;
	public $type;
	public $description;
	public $biz_rule;
	public $data;

	/**
	 *
	 * @var Item 
	 */
	private $_item;
	
	private $_children;

	/**
	 * 
	 * @param Item $item
	 * @param array $config
	 */
	public function __construct($item, $config = array())
	{
		$this->_item = $item;
		if ($item !== null) {
			$this->name = $item->name;
			$this->type = $item->type;
			$this->description = $item->description;
			$this->biz_rule = $item->bizRule;
			$this->data = $this->data;
		}
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'type'], 'required'],
			[['type'], 'integer'],
			[['description', 'biz_rule', 'data'], 'string'],
			[['name'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => 'Name',
			'type' => 'Type',
			'description' => 'Description',
			'biz_rule' => 'Biz Rule',
			'data' => 'Data',
		];
	}

	public function getIsNewRecord()
	{
		return $this->_item === null;
	}

	public static function find($id)
	{
		$item = Yii::$app->authManager->getItem($id);
		if ($item !== null) {
			return new self($item);
		}
		return null;
	}

	public function save()
	{
		if ($this->_item === null) {
			$this->_item = Yii::$app->authManager->createItem($this->name, $this->type, $this->description, $this->biz_rule, $this->data);
		} else {
			$this->_item->name = $this->name;
			$this->_item->type = $this->type;
			$this->_item->description = $this->description;
			$this->_item->bizRule = $this->biz_rule;
			$this->_item->data = $this->data;
			$this->_item->save();
		}
		return true;
	}

	public function __call($name, $params)
	{
		if($this->_item !== null && $this->_item->hasMethod($name)){
			return call_user_func_array([$this->_item,$name], $params);
		}
		parent::__call($name, $params);
	}

	
	public static function getTypeName($type = null)
	{
		$result = [
			Item::TYPE_OPERATION => 'Operation',
			Item::TYPE_TASK => 'Task',
			Item::TYPE_ROLE => 'Role'
		];
		if ($type === null)
			return $result;
		return $result[$type];
	}

	private function prepareChildren(){
		$this->_children = ['roles'=>[],'routes'=>[]];
		foreach ($this->_item->getChildren() as $item) {
			if($item->type == Item::TYPE_ROLE){
				$this->_children['roles'][]=$item;
			}else{
				$this->_children['routes'][]=$item;
			}
		}
	}

	public function getRoles(){
		if($this->_children === null){
			$this->prepareChildren();
		}
		return $this->_children['roles'];
	}

	public function getRoutes(){
		if($this->_children === null){
			$this->prepareChildren();
		}
		return $this->_children['routes'];
	}
}
