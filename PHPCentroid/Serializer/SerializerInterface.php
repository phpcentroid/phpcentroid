<?php

namespace PHPCentroid\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $object): mixed;

    public function supportsSerialization(mixed $object): bool;

}