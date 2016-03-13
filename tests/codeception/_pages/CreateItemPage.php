<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents create role or permission
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CreateItemPage extends BasePage
{
    public $route = 'admin/role/create';

    /**
     * @param array $roleData
     */
    public function submit(array $roleData)
    {
        $inputTypes = [
            'name' => 'input',
            'description' => 'textarea',
            'ruleName' => 'select',
            'data' => 'textarea',
        ];
        foreach ($roleData as $field => $value) {
            $inputType = isset($inputTypes[$field]) ? $inputTypes[$field] : 'input';
            $this->actor->fillField($inputType . '[name="AuthItem[' . $field . ']"]', $value);
        }
        $this->actor->click('submit-button');
    }
}
