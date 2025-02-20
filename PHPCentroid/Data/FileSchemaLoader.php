<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\TextUtils;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Attribute\SerializedPath;

class FileSchemaLoader extends SchemaLoader
{

    protected string $rootDir;
    private Serializer $serializer;

    public function __construct(DataApplication $application)
    {
        parent::__construct($application);
        // set root directory
        $this->rootDir =  TextUtils::join_path( $application->cwd, 'config', 'models');
        $this->serializer = new Serializer([
            new ArrayDenormalizer(),
            new DataFieldCollectionNormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
        ], [
            new JsonEncoder()
        ]);
    }

    protected function read(): void {
        $files = glob($this->rootDir . DIRECTORY_SEPARATOR . '*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                $string = file_get_contents($file);
                /**
                 * @var DataModelProperties $schema
                 */
                $schema = $this->serializer->deserialize($string, DataModelProperties::class, 'json');
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