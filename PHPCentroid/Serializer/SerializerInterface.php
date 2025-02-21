<?php

namespace PHPCentroid\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $object);

    public function supportsSerialization(mixed $object): bool;

}