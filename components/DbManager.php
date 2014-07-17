<?php

namespace mdm\admin\components;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\di\Instance;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Assignment;
use yii\rbac\Rule;
use yii\caching\Cache;
use yii\caching\TagDependency;

/**
 * Description of DbManager
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class DbManager extends \yii\rbac\BaseManager
{
    const PART_ITEMS = 'mdm.admin.items';
    const PART_CHILDREN = 'mdm.admin.children';
    const PART_RULES = 'mdm.admin.rules';

    /**
     * @var Connection|string the DB connection object or the application component ID of the DB connection.
     * After the DbManager object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     */
    public $db = 'db';

    /**
     * @var string the name of the table storing authorization items. Defaults to "auth_item".
     */
    public $itemTable = '{{%auth_item}}';

    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
     */
    public $itemChildTable = '{{%auth_item_child}}';

    /**
     * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
     */
    public $assignmentTable = '{{%auth_assignment}}';

    /**
     * @var string the name of the table storing rules. Defaults to "auth_rule".
     */
    public $ruleTable = '{{%auth_rule}}';

    /**
     *
     * @var boolean 
     */
    public $enableCaching = false;

    /**
     *
     * @var string|Cache  
     */
    public $cache = 'cache';

    /**
     *
     * @var integer 
     */
    public $cacheDuration = 0;

    /**
     * @var Item[] 
     * itemName => item
     */
    private $_items;

    /**
     * @var array 
     * itemName => childName[]
     */
    private $_children;

    /**
     * @var array
     * userId => itemName[]
     */
    private $_assignments = [];

    /**
     * @var Rule[]
     * ruleName => rule
     */
    private $_rules;

    /**
     * Initializes the application component.
     * This method overrides parent implementation by loading the authorization data
     * from PHP script.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        if ($this->enableCaching) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
        } else {
            $this->cache = null;
        }
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $this->loadItems();
        $this->loadChildren();
        $this->loadRules();
        $assignments = $this->getAssignments($userId);
        return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        $this->loadAssigments($userId);
        return $this->_assignments[$userId];
    }

    /**
     * Performs access check for the specified user.
     * This method is internally called by [[checkAccess()]].
     *
     * @param string|integer $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return boolean whether the operations can be performed by the user.
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        if (!isset($this->_items[$itemName])) {
            return false;
        }

        /** @var Item $item */
        $item = $this->_items[$itemName];
        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission : $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (in_array($itemName, $assignments) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        foreach ($this->_children as $parentName => $children) {
            if (in_array($itemName, $children) && $this->checkAccessRecursive($user, $parentName, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function addChild($parent, $child)
    {
        $this->loadItems();
        $this->loadChildren();
        if (!isset($this->_items[$parent->name], $this->_items[$child->name])) {
            throw new InvalidParamException("Either '{$parent->name}' or '{$child->name}' does not exist.");
        }

        if ($parent->name == $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name} ' as a child of itself.");
        }
        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidParamException("Cannot add a role as a child of a permission.");
        }

        if ($this->detectLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }
        if (in_array($child->name, $this->_children[$parent->name])) {
            throw new InvalidCallException("The item '{$parent->name}' already has a child '{$child->name}'.");
        }

        $this->db->createCommand()
            ->insert($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name])
            ->execute();

        $this->_children[$parent->name][] = $child->name;

        $this->invalidate(self::PART_CHILDREN);
        return true;
    }

    /**
     * Checks whether there is a loop in the authorization item hierarchy.
     *
     * @param Item $parent parent item
     * @param Item $child the child item that is to be added to the hierarchy
     * @return boolean whether a loop exists
     */
    protected function detectLoop($parent, $child)
    {
        if ($child->name === $parent->name) {
            return true;
        }
        if (!isset($this->_children[$child->name], $this->_items[$parent->name])) {
            return false;
        }
        foreach ($this->_children[$child->name] as $grandchild) {
            if ($this->detectLoop($parent, $this->_items[$grandchild])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($parent, $child)
    {
        $result = $this->db->createCommand()
                ->delete($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name])
                ->execute() > 0;
        if ($this->_children !== null) {
            $query = (new Query)
                ->select('child')
                ->from($this->itemChildTable)
                ->where(['parent' => $parent->name]);
            $this->_children[$parent->name] = $query->column($this->db);
        }
        $this->invalidate(self::PART_CHILDREN);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasChild($parent, $child)
    {
        $this->loadChildren();
        return in_array($child->name, $this->_children[$parent->name]);
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId, $ruleName = null, $data = null)
    {
        $assignment = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_id' => $assignment->userId,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
            ])->execute();

        if (isset($this->_assignments[$userId]) && !in_array($role->name, $this->_assignments[$userId])) {
            $this->_assignments[$userId][] = $role->name;
        }
        return $assignment;
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userId)
    {
        $result = $this->db->createCommand()
                ->delete($this->assignmentTable, ['user_id' => $userId, 'item_name' => $role->name])
                ->execute() > 0;

        unset($this->_assignments[$userId]);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function revokeAll($userId)
    {
        $result = $this->db->createCommand()
                ->delete($this->assignmentTable, ['user_id' => $userId])
                ->execute() > 0;

        $this->_assignments[$userId] = [];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        $this->loadItems();
        $this->loadAssigments($userId);
        if (in_array($roleName, $this->_assignments[$userId]) && isset($this->_items[$roleName])) {
            return $this->_items[$roleName];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getItems($type)
    {
        $this->loadItems();
        $items = [];

        foreach ($this->_items as $name => $item) {
            /** @var Item $item */
            if ($item->type == $type) {
                $items[$name] = $item;
            }
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    public function removeItem($item)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->delete($this->itemChildTable, ['or', 'parent=:name', 'child=:name'], [':name' => $item->name])
                ->execute();
            $this->db->createCommand()
                ->delete($this->assignmentTable, ['item_name' => $item->name])
                ->execute();
        }

        $this->db->createCommand()
            ->delete($this->itemTable, ['name' => $item->name])
            ->execute();

        $this->_assignments = [];
        $this->_children = $this->_items = null;
        $this->invalidate([self::PART_ITEMS,  self::PART_CHILDREN]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getItem($name)
    {
        $this->loadItems();
        return isset($this->_items[$name]) ? $this->_items[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function updateRule($name, $rule)
    {
        if (!$this->supportsCascadeUpdate() && $rule->name !== $name) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => $rule->name], ['rule_name' => $name])
                ->execute();
        }

        $rule->updatedAt = time();

        $this->db->createCommand()
            ->update($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'updated_at' => $rule->updatedAt,
                ], [
                'name' => $name,
            ])->execute();

        if ($rule->name !== $name) {
            $this->_items = null;
            $this->invalidate(self::PART_ITEMS);
        }
        if ($this->_rules !== null) {
            unset($this->_rules[$name]);
            $this->_rules[$rule->name] = $rule;
        }
        $this->invalidate(self::PART_RULES);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getRule($name)
    {
        $this->loadRules();
        return isset($this->_rules[$name]) ? $this->_rules[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        $this->loadRules();
        return $this->_rules;
    }

    /**
     * @inheritdoc
     */
    public function getRolesByUser($userId)
    {
        $this->loadItems();
        $roles = [];
        foreach ($this->getAssignments($userId) as $name) {
            $roles[$name] = $this->_items[$name];
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByRole($roleName)
    {
        $this->loadItems();
        $this->loadChildren();
        $result = [];
        $this->getChildrenRecursive($roleName, $result);
        if (empty($result)) {
            return [];
        }
        $permissions = [];
        foreach (array_keys($result) as $itemName) {
            if (isset($this->_items[$itemName]) && $this->_items[$itemName] instanceof Permission) {
                $permissions[$itemName] = $this->_items[$itemName];
            }
        }
        return $permissions;
    }

    /**
     * Recursively finds all children and grand children of the specified item.
     *
     * @param string $name the name of the item whose children are to be looked for.
     * @param array $result the children and grand children (in array keys)
     */
    protected function getChildrenRecursive($name, &$result)
    {
        if (isset($this->_children[$name])) {
            foreach ($this->_children[$name] as $child) {
                $result[$child] = true;
                $this->getChildrenRecursive($child, $result);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByUser($userId)
    {
        $this->loadItems();
        $this->loadChildren();
        $result = [];
        foreach ($this->getAssignments($userId) as $roleName) {
            $this->getChildrenRecursive($roleName, $result);
        }

        if (empty($result)) {
            return [];
        }

        $permissions = [];
        foreach (array_keys($result) as $itemName) {
            if (isset($this->_items[$itemName]) && $this->_items[$itemName] instanceof Permission) {
                $permissions[$itemName] = $this->_items[$itemName];
            }
        }
        return $permissions;
    }

    /**
     * @inheritdoc
     */
    public function getChildren($name)
    {
        $this->loadItems();
        $this->loadChildren();
        $items = [];
        if (isset($this->_children[$name])) {
            foreach ($this->_children[$name] as $itemName) {
                $items[$itemName] = $this->_items[$itemName];
            }
        }
        return $items;
    }

    /**
     * @inheritdoc
     */
    public function removeAll()
    {
        $this->_children = [];
        $this->_items = [];
        $this->_assignments = [];
        $this->_rules = [];
        $this->removeAllAssignments();
        $this->db->createCommand()->delete($this->itemChildTable)->execute();
        $this->db->createCommand()->delete($this->itemTable)->execute();
        $this->db->createCommand()->delete($this->ruleTable)->execute();

        $this->invalidate([self::PART_ITEMS,  self::PART_CHILDREN,  self::PART_RULES]);
    }

    /**
     * @inheritdoc
     */
    public function removeAllPermissions()
    {
        $this->removeAllItems(Item::TYPE_PERMISSION);
    }

    /**
     * @inheritdoc
     */
    public function removeAllRoles()
    {
        $this->removeAllItems(Item::TYPE_ROLE);
    }

    /**
     * Removes all auth items of the specified type.
     * @param integer $type the auth item type (either Item::TYPE_PERMISSION or Item::TYPE_ROLE)
     */
    protected function removeAllItems($type)
    {
        if (!$this->supportsCascadeUpdate()) {
            $names = (new Query)
                ->select(['name'])
                ->from($this->itemTable)
                ->where(['type' => $type])
                ->column($this->db);
            if (empty($names)) {
                return;
            }
            $key = $type == Item::TYPE_PERMISSION ? 'child' : 'parent';
            $this->db->createCommand()
                ->delete($this->itemChildTable, [$key => $names])
                ->execute();
            $this->db->createCommand()
                ->delete($this->assignmentTable, ['item_name' => $names])
                ->execute();
        }
        $this->db->createCommand()
            ->delete($this->itemTable, ['type' => $type])
            ->execute();

        $this->_assignments = [];
        $this->_children = $this->_items = null;

        $this->invalidate([self::PART_ITEMS,  self::PART_CHILDREN]);
    }

    /**
     * @inheritdoc
     */
    public function removeAllRules()
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['ruleName' => null])
                ->execute();
        }

        $this->db->createCommand()->delete($this->ruleTable)->execute();
        $this->_rules = [];
        $this->_items = null;

        $this->invalidate([self::PART_ITEMS,  self::PART_RULES]);
    }

    /**
     * @inheritdoc
     */
    public function removeAllAssignments()
    {
        $this->db->createCommand()->delete($this->assignmentTable)->execute();
        $this->_assignments = [];
    }

    /**
     * @inheritdoc
     */
    protected function removeRule($rule)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => null], ['rule_name' => $rule->name])
                ->execute();
        }

        $this->db->createCommand()
            ->delete($this->ruleTable, ['name' => $rule->name])
            ->execute();

        if ($this->_rules !== null) {
            unset($this->_rules[$rule->name]);
        }
        $this->_items = null;

        $this->invalidate([self::PART_ITEMS, self::PART_RULES]);
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addRule($rule)
    {
        $time = time();
        if ($rule->createdAt === null) {
            $rule->createdAt = $time;
        }
        if ($rule->updatedAt === null) {
            $rule->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'created_at' => $rule->createdAt,
                'updated_at' => $rule->updatedAt,
            ])->execute();

        if ($this->_rules !== null) {
            $this->_rules[$rule->name] = $rule;
        }
        $this->invalidate(self::PART_RULES);
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function updateItem($name, $item)
    {
        if (!$this->supportsCascadeUpdate() && $item->name !== $name) {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updatedAt = time();

        $this->db->createCommand()
            ->update($this->itemTable, [
                'name' => $item->name,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'updated_at' => $item->updatedAt,
                ], [
                'name' => $name,
            ])->execute();

        if ($item->name !== $name) {
            $this->_assignments = [];
            $this->_children = null;
            $this->invalidate(self::PART_CHILDREN);
        }
        $this->_items = null;
        $this->invalidate(self::PART_RULES);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }

        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ])->execute();

        if ($this->_items !== null) {
            $this->_items[$item->name] = $item;
        }
        $this->invalidate(self::PART_ITEMS);
        return true;
    }

    private function invalidate($parts)
    {
        if ($this->enableCaching) {
            TagDependency::invalidate($this->cache, $parts);
        }
    }

    private function buildKey($part)
    {
        return [__CLASS__, $part];
    }

    private function getFromCache($part)
    {
        if ($this->enableCaching) {
            return $this->cache->get($this->buildKey($part));
        }
        return false;
    }

    private function saveToCache($part, $data)
    {
        if ($this->enableCaching) {
            $this->cache->set($this->buildKey($part), $data, $this->cacheDuration, new TagDependency([
                'tags' => $part
            ]));
        }
    }

    private function loadItems()
    {
        $part = self::PART_ITEMS;
        if ($this->_items === null && ($this->_items = $this->getFromCache($part)) === false) {
            $query = (new Query)->from($this->itemTable);

            $this->_items = [];
            foreach ($query->all($this->db) as $row) {
                $this->_items[$row['name']] = $this->populateItem($row);
            }
            $this->saveToCache($part, $this->_items);
        }
    }

    private function loadChildren()
    {
        $part = self::PART_CHILDREN;
        if ($this->_children === null && ($this->_children = $this->getFromCache($part)) === false) {
            $query = (new Query)->from($this->itemChildTable);

            $this->_children = [];
            foreach ($query->all($this->db) as $row) {
                if (isset($this->_items[$row['parent']], $this->_items[$row['child']])) {
                    $this->_children[$row['parent']][] = $row['child'];
                }
            }
            $this->saveToCache($part, $this->_children);
        }
    }

    private function loadRules()
    {
        $part = self::PART_RULES;
        if ($this->_rules === null && ($this->_rules = $this->getFromCache($part)) === false) {
            $query = (new Query)->from($this->ruleTable);

            $this->_rules = [];
            foreach ($query->all($this->db) as $row) {
                $rule = @unserialize($row['data']);
                if ($rule instanceof Rule) {
                    $this->_rules[$row['name']] = $rule;
                }
            }
            $this->saveToCache($part, $this->_rules);
        }
    }

    private function loadAssigments($userId)
    {
        if (!isset($this->_assignments[$userId])) {
            $query = (new Query)
                ->select('item_name')
                ->from($this->assignmentTable)
                ->where(['user_id' => $userId]);

            $this->_assignments[$userId] = $query->column($this->db);
        }
    }

    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }

    /**
     * Returns a value indicating whether the database supports cascading update and delete.
     * The default implementation will return false for SQLite database and true for all other databases.
     * @return boolean whether the database supports cascading update and delete.
     */
    protected function supportsCascadeUpdate()
    {
        return strncmp($this->db->getDriverName(), 'sqlite', 6) !== 0;
    }
}