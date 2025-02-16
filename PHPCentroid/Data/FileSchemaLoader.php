<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\TextUtils;

class FileSchemaLoader extends SchemaLoader
{

    protected string $rootDir;

    public function __construct(DataApplication $application)
    {
        parent::__construct($application);
        // set root directory
        $this->rootDir =  TextUtils::join_path( $application->cwd, 'config', 'models');
    }

    protected function read(): void {
        $files = glob($this->rootDir . DIRECTORY_SEPARATOR . '*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                $string = file_get_contents($file);
                $json = json_decode($string, true);
                $this->set($json);
            }
        }
    }

    public function get(string $name): ?DataModel
    {
        if (empty($this->schemas)) {
            $this->read();
        }
        return parent::get($name);
    }

}