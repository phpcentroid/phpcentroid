<?php

namespace PHPCentroid\Tests\Data;

use Exception;
use PHPCentroid\Data\DataField;
use PHPCentroid\Data\DataModel;
use PHPCentroid\Tests\App\TestApplication;
use PHPUnit\Framework\TestCase;

class DataModelTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_get_json_model()
    {
        $app = new TestApplication();
        $model = $app->getConfiguration()->getModel('Thing');
        $this->assertTrue($model instanceof DataModel, '$model must be an install of DataModel class');
        $this->assertTrue($model->properties->name == 'Thing', '$model->properties->name must be "Thing"');
    }

    public function testGetAttributes()
    {
        $app = new TestApplication();
        $context = $app->createContext();
        $model = $context->getModel('User');
        $attributes = $model->getAttributes();
        $field = $attributes->get('id');
        $this->assertTrue($field instanceof DataField, '$field must be an install of DataField class');
        $this->assertTrue($field->isInheritedFrom() == 'Account', '$field->isInheritedFrom() must be "Thing"');
    }


}
