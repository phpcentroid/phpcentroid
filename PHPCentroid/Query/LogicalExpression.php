<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 07:26
 */

namespace PHPCentroid\Query;

use PHPCentroid\Common\Args;

class LogicalExpression extends DataQueryExpression
{
    /**
     * Gets or sets a string which represents the logical operator.
     * @var string
     */
    public $operator;
    /**
     * Gets or sets an array which represents the arguments of this expression.
     * @var string
     */
    public $args = array();

    const OPERATOR_REGEX = '/^(and|or|not|nor)$/';
    const OPERATOR_AND = 'and';
    const OPERATOR_OR = 'or';
    const OPERATOR_NOT = 'not';
    const OPERATOR_NOR = 'nor';

    /**
     * MethodCallExpression constructor.
     * @param string $op - The logical operator
     * @param array $args - An array of arguments
     */
    public function __construct(string $op, array $args)
    {
        Args::check(preg_match(LogicalExpression::OPERATOR_REGEX,$op)>0, 'Invalid operator');
        $this->operator = $op;
        $this->args = $args;
    }

    public function to_str($formatter = NULL)
    {
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        $array = array();
        foreach ($this->args as $val) {
            $array[] = DataQueryExpression::escape($val);
        }
        return '('.implode(' '.$this->operator.' ', $array).')';
    }
}