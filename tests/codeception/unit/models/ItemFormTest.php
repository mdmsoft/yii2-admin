<?php

namespace tests\codeception\unit\models;

use Yii;
use tests\TestCase;
use Codeception\Specify;

class ItemFormTest extends TestCase
{

    use Specify;

    protected function setUp()
    {
        (new \yii\rbac\DbManager())->removeAll();
    }

    public function testAddNew()
    {
        $model = $this->getMock('mdm\admin\models\AuthItem', ['validate']);
        $model->expects($this->once())->method('validate')->will($this->returnValue(true));

        $model->attributes = [
            'name' => 'Tester',
            'type' => 1,
        ];

        $model->save();
    }
}
