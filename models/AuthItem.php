<?php

namespace mdm\admin\models;

use Yii;
use yii\rbac\Item;
use yii\helpers\Json;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $ruleName
 * @property string $data
 *
 * @property Item $item
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AuthItem extends \yii\base\Model
{
    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;
    private $_data;

    /**
     * @var Item
     */
    private $_item;

    /**
     * Initialize object
     * @param Item  $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->type = $item->type;
            $this->description = $item->description;
            $this->ruleName = $item->ruleName;
            $this->data = $item->data === null ? null : Json::encode($item->data);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruleName'], 'in',
                'range' => array_keys(Yii::$app->authManager->getRules()),
                'message' => 'Rule not exists'],
            [['name', 'type'], 'required'],
            [['name'], 'unique', 'when' => function() {
                return $this->isNewRecord || ($this->_item->name != $this->name);
            }],
            [['type'], 'integer'],
            [['description', 'data', 'ruleName'], 'default'],
            [['name'], 'string', 'max' => 64],
            [['data'], 'jsonDecode'],
        ];
    }

    public function unique()
    {
        $authManager = Yii::$app->authManager;
        $value = $this->name;
        if ($authManager->getRole($value) !== null || $authManager->getPermission($value) !== null) {
            $message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
            $params = [
                'attribute' => $this->getAttributeLabel('name'),
                'value' => $value,
            ];
            $this->addError('name', Yii::$app->getI18n()->format($message, $params, Yii::$app->language));
        }
    }

    public function jsonDecode()
    {
        if (is_array($this->data)) {
            $this->addError('data', Yii::t('rbac-admin', 'Invalid JSON data.'));
            return;
        }
        $decode = json_decode((string) $this->data, true);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $this->_data = $decode;
                break;
            case JSON_ERROR_DEPTH:
                $this->addError('data', 'The maximum stack depth has been exceeded.');
                break;
            case JSON_ERROR_CTRL_CHAR:
                $this->addError('data', 'Control character error, possibly incorrectly encoded.');
                break;
            case JSON_ERROR_SYNTAX:
                $this->addError('data', 'Syntax error.');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $this->addError('data', 'Invalid or malformed JSON.');
                break;
            case JSON_ERROR_UTF8:
                $this->addError('data', 'Malformed UTF-8 characters, possibly incorrectly encoded.');
                break;
            default:
                $this->addError('data', 'Unknown JSON decoding error.');
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'type' => Yii::t('rbac-admin', 'Type'),
            'description' => Yii::t('rbac-admin', 'Description'),
            'ruleName' => Yii::t('rbac-admin', 'Rule Name'),
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    /**
     * Check if is new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find role
     * @param string $id
     * @return null|\self
     */
    public static function find($id)
    {
        $item = Yii::$app->authManager->getRole($id);
        $item = $item ? : Yii::$app->authManager->getPermission($id);
        if ($item !== null) {
            return new self($item);
        }

        return null;
    }

    /**
     * Save role to [[\yii\rbac\authManager]]
     * @return boolean
     */
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
            $this->_item->ruleName = $this->ruleName;
            $this->_item->data = $this->_data;
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

    /**
     * 
     * @param Item $a
     * @param Item $b
     * @return int 
     */
    public static function compare($a, $b)
    {
        if ($a->type != $b->type) {
            return $a->type > $b->type ? 1 : -1;
        } elseif (($a->name[0] == '/' || $b->name[0] == '/') && ($a->name[0] != $b->name[0])) {
            return $a->name[0] == '/' ? 1 : -1;
        } else {
            return $a->name > $b->name ? 1 : -1;
        }
    }

    /**
     * Get type name
     * @param  mixed $type
     * @return string|array
     */
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
    private $_avaliables;
    private static $_rules;
    private $_children;

    public function getChildren()
    {
        if ($this->_children === null) {
            $manager = Yii::$app->getAuthManager();
            $this->_children = array_values($manager->getChildren($this->name));
            usort($this->_children, [get_called_class(), 'compare']);
        }
        return $this->_children;
    }

    public function getAvaliables()
    {
        if ($this->_avaliables === null) {
            $manager = Yii::$app->getAuthManager();
            $items = [];
            if ($this->type == Item::TYPE_ROLE) {
                $items = array_merge($manager->getRoles(), $manager->getPermissions());
            } elseif ($this->type == Item::TYPE_PERMISSION) {
                $items = $manager->getPermissions();
            }
            uasort($items, [get_called_class(), 'compare']);
            foreach ($this->getChildren() as $item) {
                unset($items[$item->name]);
            }
            $this->_avaliables = array_values($items);
        }
        return $this->_avaliables;
    }

    public function getRules()
    {
        $manager = Yii::$app->getAuthManager();
        if (self::$_rules === null) {
            self::$_rules = array_keys($manager->getRules());
        }
        return self::$_rules;
    }

    public function extraFields()
    {
        return[
            'children',
            'avaliables',
            'rules',
        ];
    }
}