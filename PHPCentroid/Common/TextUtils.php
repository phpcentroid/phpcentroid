<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 16/10/2016
 * Time: 9:18 πμ
 */

namespace PHPCentroid\Common;


class TextUtils
{
    /** @noinspection DuplicatedCode */
    public static function join_path(): string
    {
        $path = '';
        $arguments = func_get_args();
        $args = array();
        foreach($arguments as $a) if($a !== '') $args[] = $a;//Removes the empty elements

        $arg_count = count($args);
        for($i=0; $i<$arg_count; $i++) {
            $folder = $args[$i];

            if($i != 0 and $folder[0] == DIRECTORY_SEPARATOR) $folder = substr($folder,1); //Remove the first char if it is a '/' - and its not in the first argument
            if($i != $arg_count-1 and substr($folder,-1) == DIRECTORY_SEPARATOR) $folder = substr($folder,0,-1); //Remove the last char - if its not in the last argument

            $path .= $folder;
            if($i != $arg_count-1) $path .= DIRECTORY_SEPARATOR; //Add the '/' if it's not the last element.
        }
        return $path;
    }

    public static function is_empty($string): bool
    {
        if (is_null($string)) { return true; }
        Args::string($string,"Argument");
        return (strlen($string)==0);
    }

    public static function is_blank($string): bool
    {
        if (is_null($string)) { return true; }
        Args::string($string,"Argument");
        return (strlen($string)==0) || preg_match('/^\s+$/', $string);
    }
}