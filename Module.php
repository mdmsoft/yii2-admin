<?php

namespace mdm\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Module
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{

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
     *
     * @var string
     */
    public $mainLayout = '@mdm/admin/views/layouts/main.php';

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
}
