<?php

namespace PHPCentroid\Tests\Serializer;

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
        $str = $jsonSerializer->serialize(new Person('John', 30, 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', $str);
        $person = $jsonSerializer->deserialize($str, Person::class);
        $this->assertEquals('John', $person->name);
    }

    /**
     * @throws ReflectionException
     */
    public function testDeserialize()
    {
        $jsonSerializer = new JsonSerializer();
        $str = '{"name":"John","age":30,"city":"New York"}';
        $person = $jsonSerializer->deserialize($str, Person::class);
        $this->assertEquals('John', $person->name);
    }



    /**
     * @throws ReflectionException
     */
    public function testSerializeObject()
    {
        $jsonSerializer = new JsonSerializer();
        $str = $jsonSerializer->serialize((object)array('name' => 'John', 'age' => 30, 'city' => 'New York'));
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', $str);
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
