<?php

namespace PHPCentroid\Serializer;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

class JsonSerializer
{

    public function __construct() {
    }

    /**
     * @throws ReflectionException
     */
    public function serialize($object): ?string {
        $arr = $this->serializeAny($object);
        return json_encode($arr);
    }

    /**
     * @throws ReflectionException
     */
    public function deserialize(string $string, string $class): mixed {
        return $this->deserializeAny(json_decode($string, TRUE), new ReflectionClass($class));
    }

    /**
     * @throws ReflectionException
     */
    protected function deserializeAny(mixed $any, ?ReflectionClass $reflectionClass = NULL): mixed
    {
        if ($reflectionClass == NULL) {
            return $any;
        }
        $object = $reflectionClass->newInstance();
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
        // get annotated properties
        $annotatedClass = new AnnotatedClass($object);
        $extraProperties = $annotatedClass->getProperties();
        foreach ($extraProperties as $property) {
            $name = $property->getName();
            $exists = current(array_filter($properties, function ($prop) use($name){
                return $prop->getName() == $name;
            }));
            if ($exists === FALSE) {
                $properties[] = $property;
            }
        }
        foreach ($properties as $property) {
            $attributes = $property->getAttributes();
            $jsonIgnore = current(array_filter($attributes, function($attr) {
                return $attr instanceof JsonIgnore;
            }));
            if ($jsonIgnore) {
                continue;
            }
            $jsonProperty = current(array_filter($attributes, function($attr) {
                return $attr instanceof JsonProperty;
            }));
            $propertyName = $jsonProperty ? $jsonProperty->name : $property->getName();
            if (array_key_exists($propertyName, $any)) {
                $type = $property->getType();
                $classExists = class_exists($type);
                $value = $any[$propertyName];
                $property->setValue($object, $this->deserializeAny($value, $classExists ? new ReflectionClass($type) : NULL));
            }
        }
        return $object;

    }

    /**
     * @throws ReflectionException
     */
    protected function serializeAny(mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }
        if (is_scalar($value)) {
            return $value;
        }
        if (is_array($value)) {
            return array_map(function ($val) {
                return $this->serializeAny($val);
            }, $value);
        }
        $reflectionClass = new ReflectionClass($value);
        $arr = array();
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        if ($value instanceof stdClass) {
            $vars = get_object_vars($value);
            foreach ($vars as $key => $var) {
                if ($properties[$key] == NULL) {
                    $properties[] = new ReflectionProperty($value, $key);
                }
            }
        }
        $annotatedClass = new AnnotatedClass($value);
        $extraProperties = $annotatedClass->getProperties();
        foreach ($extraProperties as $property) {
            $name = $property->getName();
            $exists = current(array_filter($properties, function ($prop) use($name){
                return $prop->getName() == $name;
            }));
            if ($exists === FALSE) {
                $properties[] = $property;
            }
        }

        foreach ($properties as $property) {
            // Check if the property is ignored
            $attributes = $property->getAttributes();
            $jsonIgnore = current(array_filter($attributes, function($attr) {
                return $attr instanceof JsonIgnore;
            }));
            if ($jsonIgnore) {
                continue;
            }
            $jsonProperty = current(array_filter($attributes, function($attr) {
                return $attr instanceof JsonProperty;
            }));
            // Get the property name
            $propertyName = $jsonProperty ? $jsonProperty->name : $property->getName();
            // insert the property into the arr array
            $arr[$propertyName] = $this->serializeAny($property->getValue($value));
        }
        // finally, return the arr array as a string
        return $arr;
    }

}