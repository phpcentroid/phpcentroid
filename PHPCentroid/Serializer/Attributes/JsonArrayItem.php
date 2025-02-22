<?php

namespace PHPCentroid\Serializer\Attributes;


use Attribute;

#[Attribute] class JsonArrayItem
{
    private string $type;

    /**
     * @param class-string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

}