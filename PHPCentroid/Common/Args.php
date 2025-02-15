<?php

namespace PHPCentroid\Common;
use Error;
use InvalidArgumentException;

/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 2/10/2016
 * Time: 11:12 πμ
 */
class Args
{
    /**
     * Throws an invalid argument exception if the given argument is null
     * @param mixed $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function not_null($argument, string $name) {
        if (is_null($argument)) {
            throw new InvalidArgumentException($name . " may not be null");
        }
    }

    /**
     * Throws an invalid argument exception if the given argument is an empty string
     * @param mixed $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function not_empty($argument, string $name) {
        self::not_null($argument, $name);
        if (is_string($argument)) {
            if (strlen($argument) == 0)
             throw new InvalidArgumentException($name . " may not be empty");
        }
        else if (is_array($argument)) {
            if (count($argument) == 0) {
                throw new InvalidArgumentException($name . " may not be empty");
            }
        }
    }

    /**
     * Throws an invalid argument exception if the given argument is a blank string
     * @param mixed $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function not_blank($argument, string $name) {

        self::not_null($argument, $name);
        if (is_string($argument)) {
            self::not_empty($argument, $name);
            if (preg_match('/^\s+$/', $argument)) {
                throw new InvalidArgumentException($name . " may not be blank");
            }
        }
    }


    /**
     * Throws an invalid argument exception if the given expression is false
     * @param bool $expression
     * @param string $message
     * @throws Error
     */
    public static function check(bool $expression, string $message) {
        if (!$expression) {
            throw new Error($message);
        }
    }

    /**
     * Throws an invalid argument exception if the given argument is not a positive number
     * @param number $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function positive($argument, string $name) {
        if (is_numeric($argument)) {
            if ($argument<=0) {
                throw new InvalidArgumentException($name." may not be negative or zero");
            }
        }
    }

    /**
     * Throws an invalid argument exception if the given argument is not a string
     * @param mixed $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function string($argument, string $name) {
        if (is_null($argument)) {
            return;
        }
        if (!is_string($argument)) {
            throw new InvalidArgumentException($name."must be a valid string");
        }
    }

    /**
     * Throws an invalid argument exception if the given argument is a negative number
     * @param number $argument
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function not_negative($argument, string $name) {

        if (is_numeric($argument)) {
            if ($argument<0) {
                throw new InvalidArgumentException($name." may not be negative");
            }
        }
    }

}