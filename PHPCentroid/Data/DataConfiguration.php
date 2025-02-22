<?php
namespace PHPCentroid\Data;

use PHPCentroid\Common\ApplicationService;

class DataConfiguration extends ApplicationService
{
    public function __construct(DataApplication $application)
    {
        parent::__construct($application);
    }

    /**
     * @param $name
     * @return DataModel|null
     */
    public function getModel($name): ?DataModel {
        $schema = $this->application->services->get(SchemaLoader::class)->get($name);
        if ($schema) {
            return new DataModel($schema);
        }
        return NULL;
    }
}