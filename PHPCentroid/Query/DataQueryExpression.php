<?php

/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 06:38
 */

namespace PHPCentroid\Query;

abstract class DataQueryExpression
{
    /**
     * @param mixed $formatter
     * @return mixed
     */
    abstract public function to_str($formatter = NULL);

    public function __toString()
    {
        return $this->to_str();
    }

    public static function escape($value = null) {
        //0. null
        if (is_null($value))
            return 'null';
        //1. array
        if (is_array($value)) {
            $array = array();
            foreach ($value as $val) {
                $array[] = DataQueryExpression::escape($val);
            }
            return '['. implode(",", $array) . ']';
        }
        //2. datetime
        else if (is_a($value, 'DateTime')) {
            $str = $value->format('c');
            return "'$str'";
        }
        //3. boolean
        else if (is_bool($value)) {
            return $value ? 'true': 'false';
        }
        //4. numeric
        else if (is_float($value) || is_double($value) || is_int($value)) {
            return json_encode($value);
        }
        //5. string
        else if (is_string($value)) {
            return "'$value'";
        }
        //6. query expression
        else if ($value instanceof DataQueryExpression) {
            return (string)$value;
        }
        //7. other
        else {
            $str = (string)$value;
            return "'$str'";
        }
    }

}