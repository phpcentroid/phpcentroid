<?php



/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 06:39
 */

namespace PHPCentroid\Query;

use PHPCentroid\Common\Args;

abstract class SelectableExpression extends DataQueryExpression
{
    /**
     * Gets or sets a string which represents the alias of this member.
     * @var string
     */
    public $alias;
    /**
     * Gets or sets a string which represents the order of this member if this is going to be used in order expressions.
     * @var string
     */
    public $order;
    /**
     * @param ?string $alias
     * @return SelectableExpression
     */
    public function as(string $alias = NULL): SelectableExpression
    {
        $this->alias = is_null($alias) ? NULL : trim($alias);
        return $this;
    }
    const ORDER_ASCENDING = 'asc';
    const ORDER_DESCENDING = 'desc';
    const OPERATOR_REGEX = '/^(asc|desc)$/i';
    /**
     * @param string $order
     * @return SelectableExpression
     */
    public function order_by(string $order): SelectableExpression {
        Args::string($order, "Order");
        Args::check(preg_match(SelectableExpression::OPERATOR_REGEX,$order)>0, 'Invalid order operator. Expected ASC or DESC');
        $this->order = strtolower($order);
        return $this;
    }

}