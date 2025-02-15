<?php

define('BASE_PATH', realpath(dirname(__FILE__)));

spl_autoload_register('AutoLoader');

function AutoLoader($className)
{
    $file = str_replace('\\',DIRECTORY_SEPARATOR,$className);
    require_once $file . '.php';
}