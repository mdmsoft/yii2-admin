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
 * @property Item $item
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

    /**
     * 
     * @param Item $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->type = $item->type;
            $this->description = $item->description;
            $this->biz_rule = $item->ruleName;
            $this->data = empty($item->data) ? null : json_encode($item->data);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['biz_rule'], 'checkRule', 'skipOnEmpty' => false],
            [['name', 'type'], 'required'],
            [['type'], 'integer'],
            [['description', 'data'], 'string'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    public function checkRule($attribute)
    {
        if (empty($this->{$attribute})) {
            $this->{$attribute} = null;
        } else {
            $name = $this->{$attribute};
            if (Yii::$app->authManager->getRule($name) === null) {
                $this->addError($attribute, "Rule {$name} not found");
            }
        }
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
        $item = Yii::$app->authManager->getRole($id);
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
            $this->_item->data = empty($this->data) ? null : json_decode($this->data);
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

    /**
     * 
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
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
}