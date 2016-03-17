<?php

namespace tests\codeception\unit\models;

use Yii;
use tests\codeception\unit\TestCase;
use Codeception\Specify;
use mdm\admin\models\AuthItem;

class ItemTest extends TestCase
{

    use Specify;

    public function testAddNew()
    {
        $model = new AuthItem();
        $model->attributes = [
            'type' => 1,
        ];
        // required
        $this->assertFalse($model->validate());


        $model = new AuthItem();
        $model->attributes = [
            'name' => 'Tester',
            'type' => 1,
        ];
        $this->assertTrue($model->validate());
        $model->save();

        
        $model = new AuthItem();
        $model->attributes = [
            'name' => 'Tester',
            'type' => 1,
        ];
        // not unique
        $this->assertFalse($model->validate());
    }
}
