<?php

namespace PHPCentroid\Serializer;

use stdClass;

class ObjectSerializer  implements SerializerInterface, DeserializerInterface
{

    public function deserialize(array $data, ?string $class = NULL): mixed
    {
        return NULL;
    }

    public function serialize(mixed $object): ?string
    {
        return NULL;
    }

    public function supportsDeserialization(mixed $object): bool
    {
        return $object instanceof stdClass;
    }

    public function supportsSerialization(mixed $object): bool
    {
        return $object instanceof stdClass;
    }
}