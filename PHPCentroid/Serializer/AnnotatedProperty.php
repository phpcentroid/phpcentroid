<?php

namespace PHPCentroid\Serializer;

class AnnotatedProperty
{
    public string $class;
    private string $name;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return [
            new JsonProperty($this->name)
        ];
    }

    public  function getValue(mixed $object) {
        return $object->{$this->name};
    }

}