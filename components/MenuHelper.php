<?php
namespace mdm\admin\components;

use yii\db\Query;
use mdm\admin\models\Menu;
/**
 * Description of MenuHelper
 *
 * @author MDMunir
 */
class MenuHelper
{
	public static function getMenus()
    {
        $result = [];
        $parents = Menu::find(['menu_parent'=>null])->all();
    }
    
    protected static function getMenuChildren($parent,&$result)
    {
        $children = Menu::find(['menu_parent'=>$parent])->all();
    }
}