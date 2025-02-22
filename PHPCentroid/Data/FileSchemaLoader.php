<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\TextUtils;
use PHPCentroid\Serializer\JsonSerializer;
use Symfony\Component\Serializer\Serializer;

class FileSchemaLoader extends SchemaLoader
{

    protected string $rootDir;
    private JsonSerializer $serializer;

    public function __construct(DataApplication $application)
    {
        parent::__construct($application);
        // set root directory
        $this->rootDir =  TextUtils::join_path( $application->cwd, 'config', 'models');
        $this->serializer = new JsonSerializer();
    }

    protected function read(): void {
        $files = glob($this->rootDir . DIRECTORY_SEPARATOR . '*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                $string = file_get_contents($file);
                /**
                 * @var DataModelProperties $schema
                 */
                $schema = $this->serializer->deserialize(json_decode($string, true), DataModelProperties::class, 'json');
                $this->set($schema);
            }
        }
    }

    public function get(string $name): ?DataModelProperties
    {
        if (empty($this->schemas)) {
            $this->read();
        }
        return parent::get($name);
    }

}