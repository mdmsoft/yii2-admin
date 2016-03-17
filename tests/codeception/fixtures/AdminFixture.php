<?php

namespace tests\codeception\fixtures;

use Yii;
use yii\test\InitDbFixture;

/**
 * Description of AdminFixture
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */
class AdminFixture extends InitDbFixture
{

    public function load()
    {
        $driver = $this->db->driverName;
        $file = Yii::getAlias("@mdm/admin/migrations/schema-{$driver}.sql");
        foreach (explode(';', file_get_contents($file)) as $sql) {
            $this->db->createCommand($sql)->execute();
        }
    }
}
