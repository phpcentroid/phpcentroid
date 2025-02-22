<?php

namespace PHPCentroid\Serializer\Annotation;

use ReflectionNamedType;

class AnnotatedProperty
{
    public string $class;
    private string $name;
    private string $type;

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
        return [];
    }

    public function getType(): ReflectionNamedType
    {
        return new AnnotatedNamedType($this->type);
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public  function getValue(mixed $object) {
        return $object->{$this->name};
    }

    public  function setValue(mixed $object, mixed $value): void {
        if (is_array($object)) {
            $object[$this->name] = $value;
            return;
        }
        $object->{$this->name} = $value;
    }

}