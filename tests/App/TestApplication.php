<?php

namespace PHPCentroid\Tests\App;

use PHPCentroid\Data\DataApplication;

class TestApplication extends DataApplication
{
    public function __construct()
    {
        parent::__construct(__DIR__ . DIRECTORY_SEPARATOR . '..');
    }
}