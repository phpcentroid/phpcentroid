<?php

namespace PHPCentroid\Serializer;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use ReflectionClass;
use ReflectionException;
use phpDocumentor\Reflection\DocBlockFactory;


class AnnotatedClass
{
    public string $name;
    public array $properties = [];
    private ReflectionClass $reflectionClass;

    /**
     * @throws ReflectionException
     */
    public function __construct(mixed $objectOrClass)
    {
        $this->reflectionClass = new ReflectionClass($objectOrClass);
        $this->properties = $this->getAnnotatedProperties();
    }

    protected function getAnnotatedProperties(): array
    {

        $docComment = $this->reflectionClass->getDocComment();
        if ($docComment === false) {
            return [];
        }
        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($docComment);
        $tags = $docBlock->getTags();
        // filter out all tags that are not @property
        $tags = array_filter($tags, function($tag) {
            return $tag instanceof Property;
        });
        $properties = [];
        foreach ($tags as $tag) {
            if ($this->reflectionClass->hasProperty($tag->getVariableName())) {
                continue;
            }
            $prop = new AnnotatedProperty($this->reflectionClass->getName(), $tag->getVariableName());
            $prop->setType($tag->getType());
            $properties[] = $prop;
        }
        return $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}