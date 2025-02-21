<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\Types\ClassString;

#[\Attribute] class JsonArray
{
    private string $name;

    /**
     * @param ?string $name
     */
    public function __construct(string $name = NULL)
    {
        $this->name = $name;
    }
}