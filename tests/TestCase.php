<?php

namespace tests;

use tests\codeception\unit\fixtures\RbacFixture;

/**
 * Description of TestCase
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
abstract class TestCase extends \yii\codeception\TestCase
{

    public function fixtures()
    {
        return[
            'rbac' => RbacFixture::className(),
        ];
    }
}
