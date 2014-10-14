<?php

namespace mdm\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Module
 * 
 * @property string $mainLayout Main layout using for module.
 * Default to layout of parent module.
 * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'. * 
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'assigment';

    /**
     *
     * @var array
     */
    public $items = [];

    /**
     *
     * @var array
     */
    public $menus;
    
    /**
     * @inheritdoc
     */
    public $layout = 'left-menu';

    /**
     *
     * @var string 
     */
    private $_mainLayout;

    /**
     * Core controller of moduls.
     * @return array
     */
    protected function getCoreItems()
    {
        $config = components\Configs::instance();

        return [
            'assigment' => [
                'class' => 'mdm\admin\items\AssigmentController'
            ],
            'role' => [
                'class' => 'mdm\admin\items\RoleController'
            ],
            'permission' => [
                'class' => 'mdm\admin\items\PermissionController'
            ],
            'route' => [
                'class' => 'mdm\admin\items\RouteController'
            ],
            'rule' => [
                'class' => 'mdm\admin\items\RuleController'
            ],
            'menu' => [
                'class' => 'mdm\admin\items\MenuController',
                'visible' => $config->db !== null && $config->db->schema->getTableSchema($config->menuTable) !== null
            ],
        ];
    }

    /**
     * Normalize `controllerMap` before create controller.
     */
    private function normalizeController()
    {
        $controllers = [];
        $this->menus = [];
        $mid = '/' . $this->getUniqueId() . '/';
        $items = ArrayHelper::merge($this->getCoreItems(), $this->items);
        foreach ($items as $id => $config) {
            $label = Inflector::humanize($id);
            $visible = true;
            if (is_array($config)) {
                $label = ArrayHelper::remove($config, 'label', $label);
                $visible = ArrayHelper::remove($config, 'visible', true);
            }
            if ($visible) {
                $this->menus[] = ['label' => $label, 'url' => [$mid . $id]];
                $controllers[$id] = $config;
            }
        }
        $this->controllerMap = ArrayHelper::merge($this->controllerMap, $controllers);
    }

    /**
     * @inheritdoc
     */
    public function createController($route)
    {
        $this->normalizeController();

        return parent::createController($route);
    }

    /**
     * 
     * @return string
     */
    public function getMainLayout()
    {
        if ($this->_mainLayout === null) {
            $module = $this->module;
            while ($module !== null && $module->layout === null) {
                $module = $module->module;
            }
            if ($module !== null && is_string($module->layout)) {
                $layout = $module->layout;
            }
            if (isset($layout)) {
                if (strncmp($layout, '@', 1) === 0) {
                    $file = Yii::getAlias($layout);
                } elseif (strncmp($layout, '/', 1) === 0) {
                    $file = Yii::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
                } else {
                    $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
                }
            } else {
                $file = Yii::getAlias('@app/views/layouts/main');
            }

            if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
                return $this->_mainLayout = $file;
            }
            $view = Yii::$app->getView();
            $path = $file . '.' . $view->defaultExtension;
            if ($view->defaultExtension !== 'php' && !is_file($path)) {
                $path = $file . '.php';
            }
            return $this->_mainLayout = $path;
        }
        return $this->_mainLayout;
    }

    /**
     * 
     * @param string $layout
     */
    public function setMainLayout($layout)
    {
        $file = Yii::getAlias($layout);
        $view = Yii::$app->getView();
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }
        $this->_mainLayout = $path;
    }
}