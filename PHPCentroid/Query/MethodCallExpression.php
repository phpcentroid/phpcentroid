<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 07:26
 */

namespace PHPCentroid\Query;

use PHPCentroid\Common\Args;

class MethodCallExpression extends SelectableExpression
{
    /**
     * Gets or sets a string which represents the name of the method.
     * @var string
     */
    public $method;
    /**
     * Gets or sets an array which represents the arguments of this method.
     * @var string
     */
    public $args = array();

    /**
     * MethodCallExpression constructor.
     * @param string $method - The name of the method
     * @param array|string|DataQueryExpression $arg - An argument or an array of arguments
     */
    public function __construct(string $method, $arg = NULL)
    {
        Args::not_blank($method, "Method");
        if (!is_null($arg)) {
            if (is_array($arg)) {
                foreach ($arg as $arg1) {
                    $this->do_add_argument($arg1);
                }
            }
            else {
                $this->do_add_argument($arg);
            }
        }
        $this->method = $method;
    }

    private function do_add_argument($arg) {
        if (is_string($arg)) {
            $this->args[] = new MemberExpression($arg);
        }
        else {
            Args::check($arg instanceof DataQueryExpression, "Argument must be a valid query expression");
            $this->args[] = $arg;
        }
    }

    public function to_str($formatter = NULL)
    {
        $array = array();
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        return $this->method.'('.implode(',',$array).')';
    }
}