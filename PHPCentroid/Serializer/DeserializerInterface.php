<?php

namespace PHPCentroid\Serializer;

interface DeserializerInterface
{
    /**
     * @param array $input
     * @param class-string $class
     * @return mixed
     */
    public function deserialize(mixed $input, ?string $class = NULL): mixed;

    public function supportsDeserialization(mixed $input, ?string $type = NULL): bool;
}