<?php

namespace mdm\admin\models;

use Yii;
use yii\base\Object;
use mdm\admin\components\Helper;

/**
 * Description of Assignment
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */
class Assignment extends Object
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
        $manager = Yii::$app->getAuthManager();
        $success = 0;
        foreach ($items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ? : $manager->getPermission($name);
                $manager->assign($item, $this->id);
                $success++;
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
        $manager = Yii::$app->getAuthManager();
        $success = 0;
        foreach ($items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ? : $manager->getPermission($name);
                $manager->revoke($item, $this->id);
                $success++;
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
        return $success;
    }

    /**
     * Get all avaliable and assigned roles/permission
     * @return array
     */
    public function getItems()
    {
        $manager = Yii::$app->getAuthManager();
        $avaliable = [];
        foreach (array_keys($manager->getRoles()) as $name) {
            $avaliable[$name] = 'role';
        }

        foreach (array_keys($manager->getPermissions()) as $name) {
            if ($name[0] != '/') {
                $avaliable[$name] = 'permission';
            }
        }

        $assigned = [];
        foreach ($manager->getAssignments($this->id) as $item) {
            $assigned[$item->roleName] = $avaliable[$item->roleName];
            unset($avaliable[$item->roleName]);
        }

        return[
            'avaliable' => $avaliable,
            'assigned' => $assigned
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
