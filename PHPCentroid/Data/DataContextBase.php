<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\Application;

abstract class DataContextBase
{
    public function __construct()
    {

    }

    public abstract function getApplication(): Application;
    public abstract function setApplication(Application $application): void;
    public abstract function getModel(string $name): ?DataModelBase;

}