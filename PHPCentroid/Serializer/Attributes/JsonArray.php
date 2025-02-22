<?php

namespace PHPCentroid\Serializer\Attributes;

use Attribute;

#[Attribute] class JsonArray
{
    private ?string $name;

    /**
     * @param ?string $name
     */
    public function __construct(?string $name = NULL)
    {
        $this->name = $name;
    }

    public function getName(): ?string {
        return $this->name;
    }
}