<?php

namespace PHPCentroid\Query;

class CountExpression extends MethodCallExpression
{
    public function __construct($arg = NULL)
    {
        if (is_null($arg)) {
            parent::__construct('count');
            return;
        }
        parent::__construct('count', array($arg));
    }
}