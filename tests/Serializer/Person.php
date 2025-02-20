<?php

namespace PHPCentroid\Tests\Serializer;

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