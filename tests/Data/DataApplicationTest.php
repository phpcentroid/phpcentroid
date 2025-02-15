<?php

namespace Data;

class ApplicationService1 extends \PHPCentroid\Common\ApplicationService {

    public function get_message() {
        return 'Hello';
    }
}

use PHPCentroid\Data\DataApplication;
use PHPUnit\Framework\TestCase;

class DataApplicationTest extends TestCase
{
    public function testServicesProperty()
    {
        $application = new \PHPCentroid\Data\DataApplication();
        $application->services->set(new ApplicationService1($application));
        $service = $application->services->get(ApplicationService1::class);
        $this->assertTrue($service instanceof ApplicationService1, 'Expected ApplicationService1');
        $this->assertTrue($service->get_message() === 'Hello', 'Expected Hello');

    }
}
