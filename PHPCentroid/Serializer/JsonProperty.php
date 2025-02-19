<?php

namespace PHPCentroid\Serializer;

class JsonProperty
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}