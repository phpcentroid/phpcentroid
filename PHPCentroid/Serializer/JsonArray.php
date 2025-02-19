<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\Types\ClassString;

class JsonArray
{
    public ClassString $type;

    public function __construct(ClassString $type)
    {
        $this->type = $type;
    }
}