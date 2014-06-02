<?php

namespace mdm\admin;

use mdm\admin\components\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Description of Module
 *
 * @author MDMunir
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $defaultRoute = 'assigment';

    /**
     *
     * @var array 
     */
    public $allowActions = [];
    public $items = [];
    public $menus;

    public function init()
    {
        parent::init();
    }

    /**
     * 
     * @param \yii\web\Application $app
     */
    public function bootstrap($app)
    {
        $app->attachBehavior(AccessControl::className(), new AccessControl([
            'allowActions' => $this->allowActions
        ]));
    }

    protected function getCoreItems()
    {
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
                'class' => 'mdm\admin\items\MenuController'
            ],
        ];
    }

    private function normalizeController()
    {
        $controllers = [];
        $this->menus = [];
        $mid = '/' . $this->getUniqueId() . '/';
        $items = ArrayHelper::merge($this->getCoreItems(), $this->items);
        foreach ($items as $id => $config) {
            if (empty($config)) {
                continue;
            }
            $label = is_array($config) ? ArrayHelper::remove($config, 'label') : null;
            $label = $label !== null ? $label : Inflector::humanize($id);
            $this->menus[] = ['label' => $label, 'url' => [$mid . $id]];
            if ($config !== true) {
                $controllers[$id] = $config;
            }
        }
        $this->controllerMap = ArrayHelper::merge($this->controllerMap, $controllers);
    }

    public function createController($route)
    {
        $this->normalizeController();
        return parent::createController($route);
    }
}