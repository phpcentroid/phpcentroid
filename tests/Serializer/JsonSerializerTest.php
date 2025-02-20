<?php

namespace PHPCentroid\Tests\Serializer;

use PHPCentroid\Data\DataFieldCollection;
use PHPCentroid\Data\DataModelProperties;
use PHPCentroid\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class JsonSerializerTest extends TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testSerialize()
    {
        $jsonSerializer = new JsonSerializer();
        $output = $jsonSerializer->serialize(new Person('John', 30, 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', json_encode($output));
        $person = $jsonSerializer->deserialize($output, Person::class);
        $this->assertEquals('John', $person->name);
    }

    /**
     * @throws ReflectionException
     */
    public function testDeserialize()
    {
        $jsonSerializer = new JsonSerializer();
        $input = json_decode('{"name":"John","age":30,"city":"New York"}', true);
        $person = $jsonSerializer->deserialize($input, Person::class);
        $this->assertEquals('John', $person->name);
    }



    /**
     * @throws ReflectionException
     */
    public function testSerializeObject()
    {
        $jsonSerializer = new JsonSerializer();
        $output = $jsonSerializer->serialize((object)array('name' => 'John', 'age' => 30, 'city' => 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', json_encode($output));
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializeArrayAsObject()
    {
        $jsonSerializer = new JsonSerializer();
        $output = $jsonSerializer->serialize(array('name' => 'John', 'age' => 30, 'city' => 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', json_encode($output));
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializeArrayOfItems()
    {
        $jsonSerializer = new JsonSerializer();
        $output = $jsonSerializer->serialize(array(new Person('John', 30, 'New York'), new Person('Jane', 25, 'Los Angeles')));
        $this->assertEquals('[{"name":"John","age":30,"city":"New York"},{"name":"Jane","age":25,"city":"Los Angeles"}]', json_encode($output));
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializeCustomCollection()
    {
        $jsonSerializer = new JsonSerializer();
        $string = file_get_contents(realpath('..') . '/config/models/User.json');
        $model = $jsonSerializer->deserialize(json_decode($string, true), DataModelProperties::class);
        $this->assertTrue($model instanceof DataModelProperties, '$model must be an install of DataModel class');
        $this->assertEquals('User', $model->name);
        $this->assertEquals('$model->fields must be an install of DataFieldCollection class', $model->fields instanceof DataFieldCollection);
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializeArrayAsObject()
    {
        $jsonSerializer = new JsonSerializer();
        $str = $jsonSerializer->serialize(array('name' => 'John', 'age' => 30, 'city' => 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', $str);
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializeArrayOfItems()
    {
        $jsonSerializer = new JsonSerializer();
        $str = $jsonSerializer->serialize(array(new Person('John', 30, 'New York'), new Person('Jane', 25, 'Los Angeles')));
        $this->assertEquals('[{"name":"John","age":30,"city":"New York"},{"name":"Jane","age":25,"city":"Los Angeles"}]', $str);
    }
}
