<?php

namespace PHPCentroid\Serializer;

use PHPCentroid\Serializer\Annotation\AnnotatedClass;
use PHPCentroid\Serializer\Attributes\JsonArray;
use PHPCentroid\Serializer\Attributes\JsonArrayItem;
use PHPCentroid\Serializer\Attributes\JsonIgnore;
use PHPCentroid\Serializer\Attributes\JsonProperty;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;


class JsonSerializer implements SerializerContextInterface, SerializerInterface, DeserializerInterface
{
    private array $serializers;

    public function __construct() {
        $this->serializers = [
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function serialize($object): mixed {
        if (is_null($object)) {
            return null;
        }
        if (is_scalar($object)) {
            return $object;
        }
        if (is_array($object)) {
            return array_map(function ($val) {
                return $this->serialize($val);
            }, $object);
        }
        $reflectionClass = new ReflectionClass($object);
        $arr = array();
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        if ($object instanceof stdClass) {
            $vars = get_object_vars($object);
            foreach ($vars as $key => $var) {
                if ($properties[$key] == NULL) {
                    $properties[] = new ReflectionProperty($object, $key);
                }
            }
        }
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
            $arr[$propertyName] = $this->serialize($property->getValue($object));
        }
        // finally, return the arr array as a string
        return $arr;
    }

    /**
     * @throws ReflectionException
     */
    public function deserialize(mixed $input, ?string $class = NULL): mixed {
        $data = $input;
        if ($class == NULL) {
            return $data;
        }
        $reflectionClass = new ReflectionClass($class);
        $jsonArray = current($reflectionClass->getAttributes(JsonArray::class));
        if ($jsonArray) {
            $jsonArrayItem = current($reflectionClass->getAttributes(JsonArrayItem::class));
            $arrayItemType = $jsonArrayItem->newInstance()->getType();
            $arr = $reflectionClass->newInstance();
            foreach ($data as $item) {
                $arr[] = $this->deserialize($item, $arrayItemType);
            }
            return $arr;
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
            $jsonIgnore = current($property->getAttributes(JsonIgnore::class));
            if ($jsonIgnore) {
                continue;
            }
            $jsonProperty = current($property->getAttributes(JsonProperty::class));
            // Check if the property is an array of specific items
            $jsonArrayItem = current($property->getAttributes(JsonArrayItem::class));
            $propertyName = $jsonProperty ? $jsonProperty->name : $property->getName();
            if (array_key_exists($propertyName, $data)) {
                // get the type of the property
                $type = $property->getType()->getName();
                // check if the class exists
                $classExists = class_exists($type);
                // get the value of the property
                $value = $data[$propertyName];
                if ($jsonArrayItem) {
                    // get the type of the array items
                    $arrayItemType = $jsonArrayItem->newInstance()->getType();
                    // check if property is a custom array type, otherwise use the default array type
                    $arr = class_exists($type) ? (new ReflectionClass($type))->newInstance() : array();
                    // iterate over the array items and deserialize them
                    foreach ($value as $item) {
                        $arr[] = $this->deserialize($item, class_exists($arrayItemType) ? $arrayItemType : NULL);
                    }
                    // set the array to the property
                    $property->setValue($object, $arr);
                    continue;
                }
                // deserialize the value
                $val = $this->deserialize($value, $classExists ? $type : NULL);
                // set the value
                $property->setValue($object, $val);
            }
        }
        return $object;
    }

    function getSerializers(): array
    {
        return $this->serializers;
    }

    public function supportsDeserialization(mixed $input, ?string $type = NULL): bool
    {
        return TRUE;
    }

    public function supportsSerialization(mixed $object): bool
    {
        return TRUE;
    }
}