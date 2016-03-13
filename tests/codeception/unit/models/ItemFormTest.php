<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;

class ItemFormTest extends TestCase
{

    use Specify;

    public function testAddNew()
    {
        $model = $this->getMock('mdm\admin\models\ContactForm', ['validate']);
        $model->expects($this->once())->method('validate')->will($this->returnValue(true));

        $model->attributes = [
            'name' => 'Tester',
            'type' => 1,
        ];

        $model->save();
    }
}
