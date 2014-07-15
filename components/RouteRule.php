<?php

namespace mdm\admin\components;

/**
 * Description of RouteRule
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class RouteRule extends \yii\rbac\Rule
{
    const RULE_NAME = 'route_rule';
        
    /**
     * 
     * @param string $user
     * @param \yii\rbac\Item $item
     * @param mixed $params
     */
    public function execute($user, $item, $params)
    {
        $routeParams = isset($item->data['params']) ? $item->data['params'] : [];
        $allow = true;
        $queryParams = \Yii::$app->request->getQueryParams();
        foreach ($routeParams as $key => $value) {
            $allow = $allow && (!isset($queryParams[$key]) || $queryParams[$key]==$value);
        }
        return $allow;
    }
}