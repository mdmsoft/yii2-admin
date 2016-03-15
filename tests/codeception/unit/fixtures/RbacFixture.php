<?php

namespace tests\codeception\unit\fixtures;

use Yii;
use yii\test\DbFixture;
use yii\rbac\DbManager;

/**
 * Description of RbacFixture
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RbacFixture extends DbFixture
{

    public function afterLoad()
    {
        $auth = new DbManager([
            'db' => $this->db,
        ]);

        $auth->removeAll();
    }
}
