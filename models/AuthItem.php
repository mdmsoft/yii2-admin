<?php

namespace mdm\admin\models;

use mdm\admin\components\Configs;
use mdm\admin\components\Helper;
use mdm\admin\controllers\AssignmentController;
use mdm\admin\Module;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\rbac\Item;
use yii\rbac\Rule;

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
class AuthItem extends Model
{
    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;

    /**
     * @var Item
     */
    private $_item;

    /**
     * Initialize object
     * @param Item  $item
     * @param array $config
     */
    public function __construct($item = null, $config = [])
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
            [['ruleName'], 'checkRule'],
            [['name', 'type'], 'required'],
            [['name'], 'checkUnique', 'when' => function () {
                    return $this->isNewRecord || ($this->_item->name != $this->name);
                }],
            [['type'], 'integer'],
            [['description', 'data', 'ruleName'], 'default'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * Check role is unique
     */
    public function checkUnique()
    {
        $authManager = Configs::authManager();
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

    /**
     * Check for rule
     */
    public function checkRule()
    {
        $name = $this->ruleName;
        if (!Configs::authManager()->getRule($name)) {
            try {
                $rule = Yii::createObject($name);
                if ($rule instanceof Rule) {
                    $rule->name = $name;
                    Configs::authManager()->add($rule);
                } else {
                    $this->addError('ruleName', Yii::t('rbac-admin', 'Invalid rule "{value}"', ['value' => $name]));
                }
            } catch (\Exception $exc) {
                $this->addError('ruleName', Yii::t('rbac-admin', 'Rule "{value}" does not exists', ['value' => $name]));
            }
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
        $item = Configs::authManager()->getRole($id);
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
            $manager = Configs::authManager();
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
            $this->_item->data = $this->data === null || $this->data === '' ? null : Json::decode($this->data);
            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }
            Helper::invalidate();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds an item as a child of another item.
     * @param array $items
     * @return int
     */
    public function addChildren($items)
    {
        $manager = Configs::authManager();
        $success = 0;
        if ($this->_item) {
            foreach ($items as $name) {
                $child = $manager->getPermission($name);
                if ($this->type == Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($name);
                }
                try {
                    $manager->addChild($this->_item, $child);
                    $success++;
                } catch (\Exception $exc) {
                    Yii::error($exc->getMessage(), __METHOD__);
                }
            }
        }
        if ($success > 0) {
            Helper::invalidate();
        }
        return $success;
    }

    /**
     * Remove an item as a child of another item.
     * @param array $items
     * @return int
     */
    public function removeChildren($items)
    {
        $manager = Configs::authManager();
        $success = 0;
        if ($this->_item !== null) {
            foreach ($items as $name) {
                $child = $manager->getPermission($name);
                if ($this->type == Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($name);
                }
                try {
                    $manager->removeChild($this->_item, $child);
                    $success++;
                } catch (\Exception $exc) {
                    Yii::error($exc->getMessage(), __METHOD__);
                }
            }
        }
        if ($success > 0) {
            Helper::invalidate();
        }
        return $success;
    }

    /**
     * Get items
     * @return array
     */
    public function getItems()
    {
        $manager = Configs::authManager();
        $advanced = Configs::instance()->advanced;
        $available = [];
        if ($this->type == Item::TYPE_ROLE) {
            foreach ($manager->getRoles() as $item) {
                $name = $item->name;
                $available[$name][0] = 'role';
                $available[$name][1] = $item->description;
            }
        }
        foreach ($manager->getPermissions() as $item) {
            $name = $item->name;
            $available[$name][0] = $name[0] == '/' || $advanced && $name[0] == '@' ? 'route' : 'permission';
            $available[$name][1] = $item->description;
        }

        $assigned = [];
        foreach ($manager->getChildren($this->_item->name) as $item) {
            $assigned[$item->name][0] = $item->type == 1 ? 'role' : ($item->name[0] == '/' || $advanced && $item->name[0] == '@'
                    ? 'route' : 'permission');
            $assigned[$item->name][1] = $item->description;
            unset($available[$item->name]);
        }
        unset($available[$this->name]);
        ksort($available);
        ksort($assigned);
        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    public function getUsers()
    {
        $module = Yii::$app->controller->module;
        if (!$module || !$module instanceof Module) {
            return [];
        }
        $ctrl = $module->createController('assignment');
        $result = [];
        if ($ctrl && $ctrl[0] instanceof AssignmentController) {
            $ctrl = $ctrl[0];
            $class = $ctrl->userClassName;
            $idField = $ctrl->idField;
            $usernameField = $ctrl->usernameField;

            $manager = Configs::authManager();
            $ids = $manager->getUserIdsByRole($this->name);

            $provider = new \yii\data\ArrayDataProvider([
                'allModels' => $ids,
                'pagination' => [
                    'pageSize' => Configs::userRolePageSize(),
                ]
            ]);
            $users = $class::find()
                    ->select(['id' => $idField, 'username' => $usernameField])
                    ->where([$idField => $provider->getModels()])
                    ->asArray()->all();

            $route = '/' . $ctrl->uniqueId . '/view';
            foreach ($users as &$row) {
                $row['link'] = Url::to([$route, 'id' => $row['id']]);
            }
            $result['users'] = $users;
            $currentPage = $provider->pagination->getPage();
            $pageCount = $provider->pagination->getPageCount();
            if ($pageCount > 0) {
                $result['first'] = 0;
                $result['last'] = $pageCount - 1;
                if ($currentPage > 0) {
                    $result['prev'] = $currentPage - 1;
                }
                if ($currentPage < $pageCount - 1) {
                    $result['next'] = $currentPage + 1;
                }
            }
        }
        return $result;
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
     * Get type name
     * @param  mixed $type
     * @return string|array
     */
    public static function getTypeName($type = null)
    {
        $result = [
            Item::TYPE_PERMISSION => 'Permission',
            Item::TYPE_ROLE => 'Role',
        ];
        if ($type === null) {
            return $result;
        }

        return $result[$type];
    }
}
