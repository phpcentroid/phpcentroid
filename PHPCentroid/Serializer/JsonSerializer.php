<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\Types\ClassString;

class JsonSerializer
{
    public function __construct() {
        //
    }

    /**
     * @throws \ReflectionException
     */
    public function serialize($object): ?string
    {
        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $property->getValue($object);
        }
        return NULL;
    }

}