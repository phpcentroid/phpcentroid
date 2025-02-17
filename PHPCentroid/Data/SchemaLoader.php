<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\ApplicationService;

abstract class SchemaLoader extends ApplicationService
{

    protected array $schemas = [];

    public function __construct(DataApplication $application) {
        parent::__construct($application);
    }

    public function get(string $name): ?array {
        return $this->schemas[$name] ?? NULL;
    }
    public function set(array $schema): void {
        $this->schemas[$schema['name']] = $schema;
    }

}