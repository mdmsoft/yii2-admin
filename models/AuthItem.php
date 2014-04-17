<?php

namespace mdm\admin\models;

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
            $this->biz_rule = $item->ruleName;
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
            [['biz_rule'], 'filter', 'filter' => function($val) {
                return empty($val) ? null : $val;
            }],
            [['name', 'type'], 'required'],
            [['type'], 'integer'],
            [['description', 'data'], 'string'],
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
        $item = Yii::$app->authManager->getRole($name);
        if ($item !== null) {
            return new self($item);
        }
        return null;
    }

    public function save()
    {
        if ($this->validate()) {
            $manager = Yii::$app->authManager;
            if ($this->_item === null) {
                if ($this->type == Item::TYPE_ROLE) {
                    $this->_item = $manager->createRole($this->name);
                } else {
                    $this->_item = $manager->createPermission($this->name);
                }
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->name;
            $this->_item->description = $this->description;
            $this->_item->ruleName = $this->biz_rule;
            $this->_item->data = $this->data;
            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }
            return true;
        } else {
            return false;
        }
    }

    public function __call($name, $params)
    {
        if ($this->_item !== null && $this->_item->hasMethod($name)) {
            return call_user_func_array([$this->_item, $name], $params);
        }
        parent::__call($name, $params);
    }

    public static function getTypeName($type = null)
    {
        $result = [
            Item::TYPE_PERMISSION => 'Permission',
            Item::TYPE_ROLE => 'Role'
        ];
        if ($type === null) {
            return $result;
        }
        return $result[$type];
    }

    private function prepareChildren()
    {
        $this->_children = ['roles' => [], 'routes' => []];
        foreach ($this->_item->getChildren() as $item) {
            if ($item->type == Item::TYPE_ROLE) {
                $this->_children['roles'][] = $item;
            } else {
                $this->_children['routes'][] = $item;
            }
        }
    }

    public function getRoles()
    {
        if ($this->_children === null) {
            $this->prepareChildren();
        }
        return $this->_children['roles'];
    }

    public function getRoutes()
    {
        if ($this->_children === null) {
            $this->prepareChildren();
        }
        return $this->_children['routes'];
    }

}
