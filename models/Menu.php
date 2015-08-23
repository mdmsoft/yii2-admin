<?php

namespace mdm\admin\models;

use Yii;
use mdm\admin\classes\Configs;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id Menu id(autoincrement)
 * @property string $name Menu name
 * @property integer $parent Menu parent
 * @property string $route Route for this menu
 * @property integer $order Menu order
 * @property string $data Extra information for this menu
 *
 * @property Menu $menuParent Menu parent
 * @property Menu[] $menus Menu children
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Menu extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Configs::menuTable();
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        if (Configs::db() !== null) {
            return Configs::db();
        } else {
            return parent::getDb();
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent', 'route', 'data', 'order'], 'default'],
            [['parent'], 'exist', 'targetAttribute' => 'id'],
            [['parent'], 'filterParent','when'=>function(){return !$this->isNewRecord;}],
            [['order'], 'integer'],
            [['route'], 'in', 'range' => static::getSavedRoutes(),]
        ];
    }

    /**
     * Use to loop detected.
     */
    public function filterParent()
    {
        $value = $this->parent;
        $parent = self::findOne($value);
        $id = $this->id;
        while ($parent) {
            if ($parent->id == $id) {
                $this->addError('parent', 'A loop has been detected.');

                return;
            }
            $parent = $parent->menuParent;
        }
    }

    private $_parentName;
    public function getParentName()
    {
        if($this->_parentName === null){
            if($this->menuParent){
                $this->_parentName = $this->menuParent->name;
            }else{
                $this->_parentName = '';
            }
        }
        return $this->_parentName;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-admin', 'ID'),
            'name' => Yii::t('rbac-admin', 'Name'),
            'parent' => Yii::t('rbac-admin', 'Parent'),
            'parent_name' => Yii::t('rbac-admin', 'Parent Name'),
            'route' => Yii::t('rbac-admin', 'Route'),
            'order' => Yii::t('rbac-admin', 'Order'),
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    /**
     * Get menu parent
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent']);
    }

    /**
     * Get menu children
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['parent' => 'id']);
    }

    /**
     * Get saved routes.
     * @return array
     */
    public static function getSavedRoutes()
    {
        $result = [];
        foreach (Yii::$app->getAuthManager()->getPermissions() as $name => $value) {
            if ($name[0] === '/' && substr($name, -1) != '*') {
                $result[] = $name;
            }
        }

        return $result;
    }

    public function extraFields()
    {
        return[
            'menuParent',
            'parentName',
        ];
    }
}