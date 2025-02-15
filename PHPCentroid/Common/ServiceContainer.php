<?php

namespace PHPCentroid\Common;

use Exception;

class ServiceContainer
{
    protected array $services = array();

    public function __construct()
    {

    }

    /**
     * Get a service from the container
     * @param $ctor
     * @return mixed
     */
    public function get($ctor): mixed
    {
        return $this->services[$ctor];
    }

    /**
     * Use a service in the container
     * @param class-string $ctor The class name of the service
     * @param mixed $instance The instance of the service
     * @return void
     * @throws Exception
     */
    public function use(string $ctor, mixed $instance): void
    {
        if (!is_a($instance, ApplicationService::class)) {
            throw new Exception("Service instance is not an application service");
        }
        $this->services[$ctor] = $instance;
    }

    /**
     * Use an instance of a service in the container
     * @param mixed $instance The instance of the service
     * @return void
     * @throws Exception
     */
    public function set(mixed $instance): void
    {
        if (!is_a($instance, ApplicationService::class)) {
            throw new Exception("Service instance is not an application service");
        }
        $this->services[get_class($instance)] = $instance;
    }

    /**
     * Check if a service is in the container
     * @param class-string $ctor
     * @return bool
     */
    public function has(string $ctor): bool
    {
        return isset($this->services[$ctor]);
    }

}