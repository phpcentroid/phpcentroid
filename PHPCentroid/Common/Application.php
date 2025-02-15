<?php

namespace PHPCentroid\Common;

class Application
{
    public ServiceContainer $services;

    public function __construct()
    {
        $this->services = new ServiceContainer();
    }
}