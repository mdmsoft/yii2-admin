<?php

use tests\codeception\_pages\CreateItemPage;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that create item work');

$createItemPage = CreateItemPage::openBy($I);

$I->see('Create Role', 'h1');

$I->amGoingTo('submit contact form with no data');
$createItemPage->submit([]);
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see validations errors');
$I->see('Create Role', 'h1');
$I->see('Name cannot be blank');

$I->amGoingTo('submit contact form with correct data');
$createItemPage->submit([
    'name' => 'roleTester',
    'description' => 'Role created for test',
]);
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->dontSeeElement('#item-form');
