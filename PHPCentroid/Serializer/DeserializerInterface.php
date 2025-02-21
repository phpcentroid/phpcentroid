<?php

namespace PHPCentroid\Serializer;

interface DeserializerInterface
{
    /**
     * @param array $data
     * @param class-string $class
     * @return mixed
     */
    public function deserialize(array $data, ?string $class = NULL): mixed;

    public function supportsDeserialization(mixed $object): bool;
}