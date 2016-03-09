<?php

namespace mdm\admin\controllers;

use mdm\admin\components\ItemController;
use yii\rbac\Item;

/**
 * PermissionController implements the CRUD actions for AuthItem model.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class PermissionController extends ItemController
{
    public $type = Item::TYPE_PERMISSION;

}
