<?php

namespace mdm\admin\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $menu_id
 * @property string $menu_name
 * @property integer $menu_parent
 * @property string $menu_route
 *
 * @property Menu $menuParent
 * @property Menu[] $menus
 */
class Menu extends \yii\db\ActiveRecord
{
    public $menu_parent_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_name'], 'required'],
            [['menu_parent_name'], 'filterParent'],
            [['menu_parent_name'], 'in', 
                'range' => self::find()->select(['menu_name'])->column(),
                'message' => 'Menu "{value}" not found.'],
            [['menu_parent'], 'default'],
            [['menu_name'], 'string', 'max' => 128],
            [['menu_route'], 'string', 'max' => 256]
        ];
    }

    public function filterParent()
    {
        $value = $this->menu_parent_name;
        $parent = self::findOne(['menu_name' => $value]);
        if ($parent) {
            $id = $this->menu_id;
            $parent_id = $parent->menu_id;
            while ($parent) {
                if ($parent->menu_id == $id) {
                    $this->addError('menu_parent_name', 'Loop detected.');
                    return;
                }
                $parent = $parent->menuParent;
            }
            $this->menu_parent = $parent_id;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => 'Menu ID',
            'menu_name' => 'Menu Name',
            'menu_parent' => 'Menu Parent',
            'menu_parent_name' => 'Menu Parent',
            'menu_route' => 'Menu Route',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(Menu::className(), ['menu_id' => 'menu_parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['menu_parent' => 'menu_id']);
    }
}