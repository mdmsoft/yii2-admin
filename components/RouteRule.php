<?php

namespace mdm\admin\components;

/**
 * RouteRule Rule for check route with extra params.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RouteRule extends \yii\rbac\Rule
{
    const RULE_NAME = 'route_rule';

    /**
     * @inheritdoc
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
