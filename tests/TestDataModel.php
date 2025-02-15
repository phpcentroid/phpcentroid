<?php

use PHPCentroid\Data\DataConfiguration;
use PHPCentroid\Data\DataModel;
use PHPUnit\Framework\TestCase;

class TestDataModel extends TestCase
{
    public function test_get_json_model() {
        $conf = new DataConfiguration(realpath('.'));
        $model = $conf->get_model('Thing');
        $this->assertTrue($model instanceof DataModel, '$model must be an install of DataModel class');
    }


}
