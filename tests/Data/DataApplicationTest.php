<?php

namespace PHPCentroid\Tests\Data;

class ApplicationService1 extends \PHPCentroid\Common\ApplicationService {

    public function get_message(): string {
        return 'Hello';
    }
}

use Exception;
use PHPUnit\Framework\TestCase;

class DataApplicationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testServicesProperty()
    {
        $application = new \PHPCentroid\Data\DataApplication();
        $application->services->set(new ApplicationService1($application));
        $service = $application->services->get(ApplicationService1::class);
        $this->assertTrue($service instanceof ApplicationService1, 'Expected ApplicationService1');
        $this->assertTrue($service->get_message() === 'Hello', 'Expected Hello');

    }
}
