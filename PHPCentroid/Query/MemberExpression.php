<?php

namespace PHPCentroid\Query;

use PHPCentroid\Common\Args;

class MemberExpression extends SelectableExpression
{
    /**
     * Gets or sets a string which represents the name of this member.
     * @var string
     */
    public $name;
    /**
     * Gets or sets a string which represents the entity where this member belongs.
     * @var string
     */
    public $entity;

    /**
     * MemberExpression constructor.
     * @param string $name - The name of this member
     * @param ?string $entity - The entity of this member
     */
    public function __construct(string $name, string $entity = NULL)
    {
        Args::not_blank($name, "Member");
        $this->name = $name;
        $this->entity = $entity;
    }

    /**
     * @param string $name - The name of this member
     * @param ?string $entity - The entity of this member
     * @return MemberExpression
     */
    public static function create(string $name, string $entity = NULL): MemberExpression
    {
        return new MemberExpression($name, $entity);
    }

    public function from(string $entity): self {
        $this->entity = $entity;
        return $this;
    }

    public function as(string $alias = NULL): SelectableExpression
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @param iExpressionFormatter $formatter
     * @return string
     */
    public function to_str($formatter = NULL): string
    {
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        return $this->name;
    }
}