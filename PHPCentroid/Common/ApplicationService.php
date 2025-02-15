<?php

namespace PHPCentroid\Common;

class ApplicationService
{
    public readonly Application $application;

    public function __construct(\PHPCentroid\Common\Application $application)
    {
        $this->application = $application;
    }
}