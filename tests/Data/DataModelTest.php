<?php

namespace PHPCentroid\Tests\Data;

use PHPCentroid\Data\DataModel;
use PHPUnit\Framework\TestCase;

class DataModelTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_get_json_model()
    {
        $app = new \PHPCentroid\Data\DataApplication(realpath('..'));
        $model = $app->getConfiguration()->getModel('Thing');
        $this->assertTrue($model instanceof DataModel, '$model must be an install of DataModel class');
        $this->assertTrue($model->properties->name == 'Thing', '$model->properties->name must be "Thing"');
    }


}
