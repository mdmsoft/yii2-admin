<?php

use tests\codeception\_pages\RoutePage;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that create route work');
RoutePage::openBy($I);
$I->see('Routes', 'h1');
