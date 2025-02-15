<?php

namespace PHPCentroid\Query;

class LiteralExpression extends SelectableExpression
{

    /**
     * Gets or sets a string which represents the name of this member.
     * @var string
     */
    public $value;

    /**
     * LiteralExpression constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param mixed $value
     * @return LiteralExpression
     */
    public static function create($value): LiteralExpression {
        return new LiteralExpression($value);
    }

    public function to_str($formatter = NULL)
    {
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        return DataQueryExpression::escape($this->value);
    }



}