<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\Types\ClassString;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class JsonSerializer
{
    public function __construct() {
        //
    }

    /**
     * @throws ReflectionException
     */
    public function serialize($object): ?string
    {
        $reflectionClass = new ReflectionClass($object);
        $json = array();
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        if ($reflectionClass->name === 'stdClass') {
            $vars = get_object_vars($object);
            $props = [];
            foreach ($vars as $key => $value) {
                $props[] = new ReflectionProperty($object, $key);
            }
            $properties = array_merge($properties, $props);
        }
        foreach ($properties as $property) {
            // Check if the property is ignored
            $ignored = current($property->getAttributes(JsonIgnore::class));
            if ($ignored) {
                continue;
            }
            // Check if the property has a JsonProperty attribute
            $jsonProperty = current($property->getAttributes(JsonProperty::class));
            // Get the property name
            $propertyName = $jsonProperty ? $jsonProperty->name : $property->getName();
            // insert the property into the json array
            $json[$propertyName] = $property->getValue($object);
        }
        // finally, return the json array as a string
        return json_encode($json);
    }

}