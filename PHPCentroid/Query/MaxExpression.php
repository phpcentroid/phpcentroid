<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 08:22
 */

namespace PHPCentroid\Query;


class MaxExpression extends MethodCallExpression
{
    public function __construct($arg = NULL)
    {
        if (is_null($arg)) {
            parent::__construct('max');
            return;
        }
        parent::__construct('max', array($arg));
    }
}