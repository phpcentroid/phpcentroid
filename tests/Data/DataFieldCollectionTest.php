<?php

namespace PHPCentroid\Tests\Data;

use Exception;
use PHPCentroid\Data\DataFieldCollection;
use PHPCentroid\Data\DataFieldCollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataFieldCollectionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSerializeCollection()
    {
        $string = file_get_contents(realpath('..') . '/config/models/User.json');
        $json = json_decode($string, true);
        $stringFields = json_encode($json['fields']);
        $serializer = new Serializer([
            new ArrayDenormalizer(),
            new DataFieldCollectionNormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
        ], [
            new JsonEncoder()
        ]);
        $fields = $serializer->deserialize($stringFields, DataFieldCollection::class, 'json');
        $this->assertTrue($fields instanceof DataFieldCollection, '$fields must be an install of DataFieldCollection class');
        $this->assertTrue($fields->count() == count($json['fields']), '$fields->count() must be 3');
        $field = $fields->get('name');
        $this->assertTrue($field->name == 'name', '$field->name must be "name"');
    }


}
