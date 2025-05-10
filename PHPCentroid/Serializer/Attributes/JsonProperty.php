<?php

namespace PHPCentroid\Serializer\Attributes;

use Attribute;

#[Attribute] class JsonProperty
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }
}