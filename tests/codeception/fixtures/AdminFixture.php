<?php

namespace tests\codeception\fixtures;

use Yii;
use yii\test\DbFixture;

/**
 * Description of AdminFixture
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */
class AdminFixture extends DbFixture
{

    public function load()
    {
        ob_start();
        ob_implicit_flush(false);
        include __DIR__ . '/data/admin.php';
        ob_get_clean();
    }
}
