<?php

namespace mdm\admin\models;

use mdm\admin\components\Configs;
use mdm\admin\components\Helper;
use Yii;

/**
 * Description of Assignment
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */
class Assignment extends \mdm\admin\BaseObject
{
    /**
     * @var integer User id
     */
    public $id;
    /**
     * @var \yii\web\IdentityInterface User
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function __construct($id, $user = null, $config = array())
    {
        $this->id = $id;
        $this->user = $user;
        parent::__construct($config);
    }

    /**
     * Grands a roles from a user.
     * @param array $items
     * @return integer number of successful grand
     */
    public function assign($items)
    {
        $manager = Configs::authManager();
        $success = 0;

        $current_user_id = Yii::$app->getUser()->getId();

        foreach ($items as $name) {
            try {
                $verify_result = $manager->checkAccess($current_user_id, $name);

                Yii::debug("verify role|permission: $name, result: "
                    . ($verify_result ? "Y" : "N"));

                if ($verify_result) {
                    $item = $manager->getRole($name);
                    $item = $item ?: $manager->getPermission($name);
                    $manager->assign($item, $this->id);
                    $success++;
                }
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
        return $success;
    }

    /**
     * Revokes a roles from a user.
     * @param array $items
     * @return integer number of successful revoke
     */
    public function revoke($items)
    {
        $current_user_id = Yii::$app->getUser()->getId();
        $manager = Configs::authManager();
        $success = 0;
        foreach ($items as $name) {
            try {
                $verify_result = $manager->checkAccess($current_user_id, $name);

                Yii::debug("verify role|permission: $name, result: "
                    . ($verify_result ? "Y" : "N"));

                if ($verify_result) {
                    $item = $manager->getRole($name);
                    $item = $item ?: $manager->getPermission($name);
                    $manager->revoke($item, $this->id);
                    $success++;
                }
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
        return $success;
    }

    /**
     * Get all available and assigned roles/permission
     * @return array
     */
    public function getItems()
    {
        $current_user_id = Yii::$app->getUser()->getId();
        $manager = Configs::authManager();
        $available = [];

        $roles = $manager->getRolesByUser($current_user_id);

        foreach ($roles as $role) {
            $name = $role->name;
            $available[$name][0] = 'role';
            $available[$name][1] = $role->description;

            $child_roles = $manager->getChildRoles($name);
            foreach ($child_roles as $childRole)
            {
                $name = $childRole->name;
                $available[$name][0] = 'role';
                $available[$name][1] = $childRole->description;
            }
        }


        $permissions = $manager->getPermissionsByUser($current_user_id);

        foreach ($permissions as $permission) {
            $name = $permission->name;
            if ($name[0] != '/') {
                $available[$name][0] = 'permission';
                $available[$name][1] = $permission->description;
            }
        }

        $assigned = [];
        foreach ($manager->getAssignments($this->id) as $item) {
            if(isset($available[$item->roleName])) {
                $assigned[$item->roleName] = $available[$item->roleName];
                unset($available[$item->roleName]);
            }
        }

        return [
            'available' => $available,
            'assigned'  => $assigned,
        ];
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($this->user) {
            return $this->user->$name;
        }
    }
}
