<?php

namespace PHPCentroid\Data;

use Exception;
use PHPCentroid\Common\Application;

class DataApplication extends Application
{

    public readonly string $cwd;

    public function __construct(?string $cwd = NULL)
    {
        parent::__construct();
        $this->cwd = $cwd ?? getcwd();
        try {
            // set data configuration service
            $this->services->set(new DataConfiguration($this));
            // set schema loader service
            $this->services->use(SchemaLoader::class, new FileSchemaLoader($this));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getConfiguration(): DataConfiguration
    {
        return $this->services->get(DataConfiguration::class);
    }

    public function createContext(): DataContextBase
    {
        $context = new DataContext();
        $context->setApplication($this);
        return $context;
    }


}