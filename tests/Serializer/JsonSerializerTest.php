<?php

namespace Serializer;

use PHPCentroid\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;
use ReflectionException;


/**
 * @property string $name
 * @property int $age
 * @property string $city
 */
class Person
{

    /**
     * @param string $name
     * @param int $age
     * @param string $city
     */
    public function __construct(string $name, int $age, string $city)
    {
        $this->name = $name;
        $this->age = $age;
        $this->city = $city;
    }
}

class JsonSerializerTest extends TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testSerialize()
    {
        $jsonSerializer = new JsonSerializer();
        $this->assertEquals('{"name":"John","age":30,"city":"New York"}', $jsonSerializer->serialize(new Person('John', 30, 'New York')));
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
}
