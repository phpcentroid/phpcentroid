<?php

namespace PHPCentroid\Serializer;

interface SerializerContextInterface
{
    /**
     * return (SerializerInterface|DeserializerInterface)[]
     */
    function getSerializers(): array;
}