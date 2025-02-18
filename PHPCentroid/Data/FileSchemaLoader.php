<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\TextUtils;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
                $serializer = new Serializer([
                    new ArrayDenormalizer(),
                    new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
                ], [
                    new JsonEncoder()
                ]);
                /**
                 * @var DataModelProperties $schema
                 */
                $schema = $serializer->deserialize($string, DataModelProperties::class, 'json');
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