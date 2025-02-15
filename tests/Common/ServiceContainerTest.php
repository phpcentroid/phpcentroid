<?php

namespace Common;

use Exception;
use PHPCentroid\Common\Application;
use PHPCentroid\Common\ApplicationService;
use PHPCentroid\Common\ServiceContainer;
use PHPUnit\Framework\TestCase;

class Service1 extends ApplicationService {
    public function __construct($application) {
        parent::__construct($application);
    }
    public function get_message() {
        return 'Hello';
    }
}

class ServiceContainerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_get_service() {
        $app = new Application();
        $container = new ServiceContainer();
        $container->use(Service1::class, new Service1($app));
        $this->assertTrue($container->get(Service1::class) instanceof Service1, 'Expected Service1');
    }

    /**
     * @throws Exception
     */
    public function test_set_service() {
        $app = new Application();
        $container = new ServiceContainer();
        $container->set(new Service1($app));
        $this->assertTrue($container->get(Service1::class) instanceof Service1, 'Expected Service1');
    }

    /**
     * @throws Exception
     */
    public function test_has_service()
    {
        $app = new Application();
        $container = new ServiceContainer();
        $container->use(Service1::class, new Service1($app));
        $this->assertTrue($container->has(Service1::class), 'Expected true');
    }

}
