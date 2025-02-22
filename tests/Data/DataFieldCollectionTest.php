<?php

namespace PHPCentroid\Tests\Data;

use Exception;
use PHPCentroid\Data\DataFieldCollection;
use PHPCentroid\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class DataFieldCollectionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSerializeCollection()
    {
        $string = file_get_contents(realpath(__DIR__ . DIRECTORY_SEPARATOR. '..') . '/config/models/User.json');
        $json = json_decode($string, true);
        $stringFields = $json['fields'];
        $serializer = new JsonSerializer();
        $fields = $serializer->deserialize($stringFields, DataFieldCollection::class);
        $this->assertTrue($fields instanceof DataFieldCollection, '$fields must be an install of DataFieldCollection class');
        $this->assertTrue($fields->count() == count($json['fields']), '$fields->count() must be 3');
        $field = $fields->get('enabled');
        $this->assertTrue($field->name == 'enabled', '$field->name must be "enabled"');
    }


}
