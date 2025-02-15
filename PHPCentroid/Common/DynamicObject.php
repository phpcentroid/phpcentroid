<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 16/10/2016
 * Time: 8:59 πμ
 */

namespace PHPCentroid\Common;


use Error;
use stdClass;

class DynamicObject {

    /**
     * @param null|array|stdClass|DynamicObject $args
     * @throws Error
     */
    public function __construct($args = null) {
        if (is_null($args))
            return;
        if (is_array($args)) {
            foreach ($args as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
        else if (is_a($args, 'stdClass')) {
            DynamicObject::object_to_dynamic($args, $this);
        }
        else if (is_a($args, 'DynamicObject')) {
            $vars = get_object_vars($args);
            foreach ($vars AS $property => $value) {
                $this->{$property} = $value;
            }
        }
        else {
            throw new Error('Invalid argument');
        }
    }

    /**
     * @param stdClass|null|DynamicObject $object
     * @param mixed $target
     * @return array|DynamicObject|null
     */
    public static function object_to_dynamic($object, $target = null) {

        if (is_null($object))
            return null;
        if (is_array($object)) {
            $arr = array();
            foreach ($object as $item) {
                $arr[] = DynamicObject::object_to_dynamic($item);
            }
            return $arr;
        }
        if (is_null($target))
            $target = new DynamicObject();
        $vars = get_object_vars($object);
        foreach ($vars AS $key => $value) {
            if (is_array($value)) {
                $arr = array();
                foreach ($value as $item) {
                    if (is_a($item, 'stdClass'))
                        $arr[] = DynamicObject::object_to_dynamic($item);
                    else
                        $arr[] = $item;
                }
                $target->{$key} = $arr;
            }
            else if (is_a($value, 'stdClass')) {
                $target->{$key} = DynamicObject::object_to_dynamic($value);
            }
            else {
                $target->{$key} = $value;
            }
        }
        return $target;
    }

    public function __call($method, $arguments) {
        $arguments = array_merge(array("DynamicObject" => $this), $arguments); // Note: method argument 0 will always refer to the main class ($this).
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new Error("Fatal error: Call to undefined method DynamicObject::$method()");
        }
    }
}