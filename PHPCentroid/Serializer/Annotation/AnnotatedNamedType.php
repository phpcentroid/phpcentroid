<?php

namespace PHPCentroid\Serializer\Annotation;

class AnnotatedNamedType extends \ReflectionNamedType
{
    private string $type;
    private bool $allows_null;

    public function __construct(string $name, bool $allowsNull = false)
    {
        $this->type = $name;
        $this->allows_null = $allowsNull;
    }

    public function getName(): string
    {
        return $this->type;
    }

    public function allowsNull(): bool
    {
        return $this->allows_null;
    }
}