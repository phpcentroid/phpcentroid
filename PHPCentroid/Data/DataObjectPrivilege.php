<?php

namespace PHPCentroid\Data;

use stdClass;

class DataObjectPrivilege extends stdClass
{
    /**
     * @var string $mask A number which represents permission mask
     * The mask is a combination of the following values: 1=Read, 2=Create, 4=Update, 8=Delete, 16=Execute
     * The mask can be calculated by adding the values of the required permissions e.g. Read+Create+Update=7
     */
    public string $mask;
    /**
     * @var string $type A string which represents the permission scope.
     */
    public string $type;
    /**
     * @var string $account A string which represents the name of the security group where this privilege will be applied e.g. Administrators, Sales etc.
     * If the current user does not belong to the specified security group, the privilege will not be granted.
     */
    public string $account;
    /**
     * @var string $filter A string which represents a filter expression for this privilege. This attribute is used for self privileges which are commonly derived from user's attributes e.g. 'owner eq me()' or 'orderStatus eq 1 and customer eq me()' etc.
     */
    public string $filter;
    /**
     * @var string $when A string which represents the "when" expression for this privilege. This attribute is being used for validating the privilege based on a condition which should be met against the current state of an object e.g. 'orderStatus eq 1' or 'customer eq me()' etc.
     */
    public string $when;
    /**
     * @var string $scope A string which represents the authentication scope of this privilege e.g. 'profile', 'profile:readonly' etc. If the current user context does not have the required scope, the privilege will not be granted.
     */
    public string $scope;
}