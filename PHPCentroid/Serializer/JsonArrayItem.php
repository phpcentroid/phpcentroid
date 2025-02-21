<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\Types\ClassString;

#[\Attribute] class JsonArrayItem
{
    public string $type;

    /**
     * @param class-string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }
}