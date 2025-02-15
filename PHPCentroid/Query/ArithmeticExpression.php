<?php

namespace PHPCentroid\Query;

use PHPCentroid\Common\Args;

class ArithmeticExpression extends SelectableExpression
{

    /**
     * Gets or sets the left operand of this expression.
     * @var DataQueryExpression
     */
    public $left;
    /**
     * Gets or sets the right operand of this expression.
     * @var DataQueryExpression
     */
    public $right;
    /**
     * Gets or sets the operator used on expression.
     * @var DataQueryExpression
     */
    public $operator;
    /**
     * ComparisonExpression constructor.
     * @param mixed $left - The left operand
     * @param string $op - The operator of this expression
     * @param mixed $right - The right operand
     */
    public function __construct($left, string $op, $right)
    {
        Args::not_null($left, 'Left operand');
        if (is_string($left))
            $this->left = new MemberExpression($left);
        else {
            Args::check($left instanceof DataQueryExpression, "Left operand must be a valid query expression or string");
            $this->left = $left;
        }
        Args::check(preg_match(ArithmeticExpression::OPERATOR_REGEX,$op)>0, 'Invalid operator');
        $this->operator = $op;
        if ($right instanceof DataQueryExpression)
            $this->right = $right;
        else
            $this->right = new LiteralExpression($right);
    }

    const OPERATOR_REGEX = '/^(add|sub|mul|div|mod)$/';

    public function to_str($formatter = NULL)
    {
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        return '('.DataQueryExpression::escape($this->left).' '.$this->operator.' '.DataQueryExpression::escape($this->right).')';
    }



}