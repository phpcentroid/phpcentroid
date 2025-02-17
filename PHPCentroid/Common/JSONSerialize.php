<?php

namespace PHPCentroid\Common;

use ReflectionClass;
use ReflectionProperty;

class JSONSerialize
{
    /**
     * @param mixed $object
     * @return string
     */
    public static function serialize(mixed $object): string
    {
        return json_encode($object);
    }

    /**
     * @param mixed $objectOrClass
     * @param string $json
     * @return mixed
     * @throws \ReflectionException
     */
    public static function unserialize(mixed $objectOrClass, string $json): mixed
    {
        $arr = json_decode($json);
        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $object = $reflect->newInstance();
        foreach($arr as $key=>$value){
            if (array_key_exists($key, $arr)) {
                if ($props[$key]->getType() == 'array') {
                    $object->{$key} = self::unserialize($objectOrClass, json_encode($value));
                } else {
                    $object->{$key} = $value;
                }
            }
        }
        return $object;
    }
}