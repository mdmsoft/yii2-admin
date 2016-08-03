<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RoutePage extends BasePage
{
    public $route = 'admin/route/index';

    /**
     * @param array $route
     */
    public function addRoute($route)
    {
        $this->actor->fillField('#inp-route', $route);
        $this->actor->click('add-route');
    }
}
