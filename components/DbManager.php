<?php

namespace mdm\admin\components;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Assignment;
use yii\rbac\Role;
use yii\rbac\Rule;
use yii\caching\Cache;
use yii\caching\TagDependency;

/**
 * DbManager represents an authorization manager that stores authorization information in database.
 *
 * The database connection is specified by [[$db]]. The database schema could be initialized by applying migration:
 *
 * ~~~
 * yii migrate --migrationPath=@yii/rbac/migrations/
 * ~~~
 *
 * If you don't want to use migration and need SQL instead, files for all databases are in migrations directory.
 *
 * You may change the names of the three tables used to store the authorization data by setting [[\yii\rbac\DbManager::$itemTable]],
 * [[\yii\rbac\DbManager::$itemChildTable]] and [[\yii\rbac\DbManager::$assignmentTable]].
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DbManager extends \yii\rbac\DbManager
{
    const PART_ITEMS = 'mdm.admin.items';
    const PART_CHILDREN = 'mdm.admin.children';
    const PART_RULES = 'mdm.admin.rules';

    /**
     * @var boolean Enable caching
     */
    public $enableCaching = false;

    /**
     * @var string|Cache Cache component
     */
    public $cache = 'cache';

    /**
     * @var integer Cache duration
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
     * @inheritdoc
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
        $this->loadAssignments($userId);

        return isset($this->_assignments[$userId]) ? $this->_assignments[$userId] : [];
    }

    /**
     * @inheritdoc
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

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
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
        parent::addChild($parent, $child);

        $this->_children[$parent->name][] = $child->name;
        $this->invalidate(self::PART_CHILDREN);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($parent, $child)
    {
        $result = parent::removeChild($parent, $child);
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

        return isset($this->_children[$parent->name]) && in_array($child->name, $this->_children[$parent->name]);
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
        $assignment = parent::assign($role, $userId);

        if (isset($this->_assignments[$userId])) {
            $this->_assignments[$userId][$role->name] = $assignment;
        }
        return $assignment;
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userId)
    {
        $result = parent::revoke($role, $userId);

        unset($this->_assignments[$userId]);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function revokeAll($userId)
    {
        if (empty($userId)) {
            return false;
        }

        $result = parent::revokeAll($userId);

        $this->_assignments[$userId] = [];

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        $this->loadItems();
        $this->loadAssignments($userId);
        if (isset($this->_assignments[$userId][$roleName], $this->_items[$roleName])) {
            return $this->_items[$roleName];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getItems($type)
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
        parent::removeItem($item);

        $this->_assignments = [];
        $this->_children = $this->_items = null;
        $this->invalidate([self::PART_ITEMS, self::PART_CHILDREN]);

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
        parent::updateRule($name, $rule);

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
        foreach ($this->getAssignments($userId) as $name => $asgn) {
            $roles[$name] = $this->_items[$name];
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByRole($roleName)
    {
        $childrenList = $this->getChildrenList();
        $result = [];
        $this->getChildrenRecursive($roleName, $childrenList, $result);
        if (empty($result)) {
            return [];
        }
        $this->loadItems();
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
    protected function getChildrenList()
    {
        $this->loadChildren();
        return $this->_children;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByUser($userId)
    {
        $childrenList = $this->getChildrenList();
        $result = [];
        foreach ($this->getAssignments($userId) as $roleName => $asgn) {
            $this->getChildrenRecursive($roleName, $childrenList, $result);
        }

        if (empty($result)) {
            return [];
        }

        $this->loadItems();
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

        $this->invalidate([self::PART_ITEMS, self::PART_CHILDREN, self::PART_RULES]);
    }

    /**
     * @inheritdoc
     */
    protected function removeAllItems($type)
    {
        parent::removeAllItems($type);

        $this->_assignments = [];
        $this->_children = $this->_items = null;

        $this->invalidate([self::PART_ITEMS, self::PART_CHILDREN]);
    }

    /**
     * @inheritdoc
     */
    public function removeAllRules()
    {
        parent::removeAllRules();

        $this->_rules = [];
        $this->_items = null;

        $this->invalidate([self::PART_ITEMS, self::PART_RULES]);
    }

    /**
     * @inheritdoc
     */
    public function removeAllAssignments()
    {
        parent::removeAllAssignments();
        $this->_assignments = [];
    }

    /**
     * @inheritdoc
     */
    protected function removeRule($rule)
    {
        parent::removeRule($rule);

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
        parent::addRule($rule);

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
        parent::updateItem($name, $item);

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
        parent::addItem($item);

        if ($this->_items !== null) {
            $this->_items[$item->name] = $item;
        }
        $this->invalidate(self::PART_ITEMS);

        return true;
    }

    /**
     * Invalidate cache
     * @param string $parts
     */
    private function invalidate($parts)
    {
        if ($this->enableCaching) {
            TagDependency::invalidate($this->cache, $parts);
        }
    }

    /**
     * Build key cache
     * @param string $part
     * @return mixed
     */
    private function buildKey($part)
    {
        return [__CLASS__, $part];
    }

    /**
     * Get data from cache
     * @param string $part
     * @return mixed
     */
    private function getFromCache($part)
    {
        if ($this->enableCaching) {
            return $this->cache->get($this->buildKey($part));
        }

        return false;
    }

    /**
     * Save data to cache
     * @param string $part
     * @param mixed $data
     */
    private function saveToCache($part, $data)
    {
        if ($this->enableCaching) {
            $this->cache->set($this->buildKey($part), $data, $this->cacheDuration, new TagDependency([
                'tags' => $part
            ]));
        }
    }

    /**
     * Load data. If avaliable in memory, get from memory
     * If no, get from cache. If no avaliable, get from database.
     */
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

    /**
     * Load data. If avaliable in memory, get from memory
     * If no, get from cache. If no avaliable, get from database.
     */
    private function loadChildren()
    {
        $this->loadItems();
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

    /**
     * Load data. If avaliable in memory, get from memory
     * If no, get from cache. If no avaliable, get from database.
     */
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

    /**
     * Load data. If avaliable in memory, get from memory
     * If no, get from cache. If no avaliable, get from database.
     */
    private function loadAssignments($userId)
    {
        if (!isset($this->_assignments[$userId]) && !empty($userId)) {
            $query = (new Query)
                ->from($this->assignmentTable)
                ->where(['user_id' => (string) $userId]);

            $this->_assignments[$userId] = [];
            foreach ($query->all($this->db) as $row) {
                $this->_assignments[$userId][$row['item_name']] = new Assignment([
                    'userId' => $row['user_id'],
                    'roleName' => $row['item_name'],
                    'createdAt' => $row['created_at'],
                ]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function removeChildren($parent)
    {
        $result = parent::removeChildren($parent);
        if ($this->_children !== null) {
            unset($this->_children[$parent->name]);
        }
        $this->invalidate(self::PART_CHILDREN);

        return $result;
    }
}