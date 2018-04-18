<?php

namespace mdm\admin\models;

use Yii;
use yii\rbac\Rule;
use mdm\admin\components\Configs;

/**
 * BizRule
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class BizRule extends \yii\base\Model
{
    /**
     * @var string name of the rule
     */
    public $name;

    /**
     * @var integer UNIX timestamp representing the rule creation time
     */
    public $createdAt;

    /**
     * @var integer UNIX timestamp representing the rule updating time
     */
    public $updatedAt;

    /**
     * @var string Rule classname.
     */
    public $className;

    /**
     * @var Rule
     */
    private $_item;

    /**
     * Initilaize object
     * @param \yii\rbac\Rule $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->className = get_class($item);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'className'], 'required'],
            [['className'], 'string'],
            [['className'], 'classExists']
        ];
    }

    /**
     * Validate class exists
     */
    public function classExists()
    {
        if (!class_exists($this->className)) {
            $message = Yii::t('rbac-admin', "Unknown class '{class}'", ['class' => $this->className]);
            $this->addError('className', $message);
            return;
        }
        if (!is_subclass_of($this->className, Rule::className())) {
            $message = Yii::t('rbac-admin', "'{class}' must extend from 'yii\rbac\Rule' or its child class", [
                    'class' => $this->className]);
            $this->addError('className', $message);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'className' => Yii::t('rbac-admin', 'Class Name'),
        ];
    }

    /**
     * Check if new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find model by id
     * @param type $id
     * @return null|static
     */
    public static function find($id)
    {
        $item = Configs::authManager()->getRule($id);
        if ($item !== null) {
            return new static($item);
        }

        return null;
    }

    /**
     * Save model to authManager
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Configs::authManager();
            $class = $this->className;
            if ($this->_item === null) {
                $this->_item = new $class();
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->name;

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
     * Get item
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }
}
