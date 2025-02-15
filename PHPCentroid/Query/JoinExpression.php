<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 18/10/2016
 * Time: 9:18 μμ
 */

namespace PHPCentroid\Query;


use PHPCentroid\Common\Args;

class JoinExpression extends DataQueryExpression
{
    const DIRECTION_LEFT = 'left';
    const DIRECTION_RIGHT = 'right';
    const DIRECTION_INNER = 'inner';
    /**
     * @var string
     */
    private $direction;
    /**
     * @var EntityExpression
     */
    private $entity;

    /**
     * @var ComparisonExpression|LogicalExpression
     */
    private $expr;

    /**
     * JoinExpression constructor.
     * @param string|EntityExpression $entity
     * @param string $direction
     */
    public function __construct($entity, string $direction = 'inner')
    {
        if (is_string($entity)) {
            $this->entity = new EntityExpression($entity);
            Args::check(preg_match('/^(inner|left|right)$/', $direction),'Invalid argument. Expected a valid join direction');
            $this->direction = $direction;
            return;
        }
        Args::check($entity instanceof EntityExpression,'Invalid argument. Expected entity');
        $this->entity = $entity;
    }

    /**
     * @param ComparisonExpression|LogicalExpression $expr
     * @return $this
     */
    public function with($expr): JoinExpression
    {
        Args::check(($expr instanceof ComparisonExpression)||($expr instanceof LogicalExpression),'Invalid argument. Expected comparison or logical expression');
        $this->expr = $expr;
        return $this;
    }

    /**
     * @param mixed $formatter
     * @return mixed
     */
    public function to_str($formatter = NULL): string
    {
        if ($formatter instanceof iExpressionFormatter)
            return $formatter->format($this);
        Args::check(is_null($this->expr),'Join expression has not been set');
        if (is_null($this->entity->alias))
            return $this->entity->name.'('.$this->expr->to_str().')';
        else
            return $this->entity->name.'('.$this->expr->to_str().') as '.$this->entity->alias;
    }
}