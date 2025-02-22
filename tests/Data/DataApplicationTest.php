<?php

namespace PHPCentroid\Tests\Data;

class ApplicationService1 extends \PHPCentroid\Common\ApplicationService {

    public function get_message(): string {
        return 'Hello';
    }
}

use Exception;
use PHPCentroid\Data\DataApplication;
use PHPCentroid\Data\DataContextBase;
use PHPCentroid\Data\DataModel;
use PHPUnit\Framework\TestCase;

class DataApplicationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testServicesProperty()
    {
        $application = new DataApplication();
        $application->services->set(new ApplicationService1($application));
        $service = $application->services->get(ApplicationService1::class);
        $this->assertTrue($service instanceof ApplicationService1, 'Expected ApplicationService1');
        $this->assertTrue($service->get_message() === 'Hello', 'Expected Hello');

    }

    public function testCreateContext()
    {
        $application = new DataApplication(realpath(__DIR__ . DIRECTORY_SEPARATOR. '..'));
        $context = $application->createContext();
        $model = $context->getModel('Thing');
        $this->assertTrue($model instanceof DataModel, 'Expected DataModel');
        $this->assertTrue($model->getName() === 'Thing', 'Expected Thing');
    }
}
