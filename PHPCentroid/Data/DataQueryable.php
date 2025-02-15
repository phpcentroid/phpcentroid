<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 16/10/2016
 * Time: 9:49 πμ
 */

namespace PHPCentroid\Data;

use PHPCentroid\Common\Args;
use PHPCentroid\Query\iQueryable;
use PHPCentroid\Query\QueryExpression;
use PHPCentroid\Query\SelectableExpression;

class DataQueryable implements iQueryable
{
    /**
     * @var DataModel
     */
    private $model;
    /**
     * @var QueryExpression
     */
    private $query;
    /**
     * DataQueryable constructor.
     * @param DataModel $model
     */
    public function __construct($model)
    {
        Args::check($model instanceof DataModel, 'Model must be an instance of DataModel class');
        $this->model = $model;
        $this->query = new QueryExpression($this->model->get_source());
    }

    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function select($arg): DataQueryable
    {
        $this->query->select($arg);
        return $this;
    }

    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function also_select($arg)
    {
        $this->query->also_select($arg);
        return $this;
    }

    public function has_fields()
    {
        return $this->query->has_fields();
    }

    public function has_filter()
    {
        return $this->query->has_filter();
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function group_by($expr)
    {
        $this->query->group_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by($expr)
    {
        $this->query->order_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function then_by($expr)
    {
        $this->query->then_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by_descending($expr)
    {
        $this->query->order_by_descending($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function then_by_descending($expr): DataQueryable
    {
        $this->query->then_by_descending($expr);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function where($arg): DataQueryable
    {
        $this->query->where($arg);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function also($arg): DataQueryable
    {
        $this->query->also($arg);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function either($arg)
    {
        $this->query->either($arg);
        return $this;
    }
    /**
     * @return $this
     */
    public function prepare()
    {
        $this->query->prepare();
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function equal($value)
    {
        $this->query->equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function not_equal($value)
    {
        $this->query->not_equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_than($value)
    {
        $this->query->lower_than($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_or_equal($value)
    {
        $this->query->lower_or_equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greater_than($value)
    {
        $this->query->greater_than($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greater_or_equal($value): DataQueryable
    {
        $this->query->greater_or_equal($value);
        return $this;
    }

    public function get_day(): DataQueryable
    {
        $this->query->get_day();
        return $this;
    }

    public function get_month()
    {
        $this->query->get_month();
        return $this;
    }

    public function get_year()
    {
        $this->query->get_year();
        return $this;
    }

    public function get_seconds()
    {
        $this->query->get_seconds();
        return $this;
    }

    public function get_minutes()
    {
        $this->query->get_minutes();
        return $this;
    }

    public function get_hours()
    {
        $this->query->get_hours();
        return $this;
    }

    public function get_date()
    {
        $this->query->get_date();
        return $this;
    }

    public function to_lower_case()
    {
        $this->query->to_lower_case();
        return $this;
    }

    public function to_upper_case()
    {
        $this->query->to_upper_case();
        return $this;
    }

    public function floor()
    {
        $this->query->floor();
        return $this;
    }

    public function ceil()
    {
        $this->query->ceil();
        return $this;
    }

    public function trim()
    {
        $this->query->trim();
        return $this;
    }

    /**
     * @return $this
     */
    public function length()
    {
        $this->query->length();
        return $this;
    }

    /**
     * @param integer $n
     * @return $this
     */
    public function round(int $n)
    {
        $this->query->round($n);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function add($x)
    {
        $this->query->add($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function subtract($x)
    {
        $this->query->subtract($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function multiply($x)
    {
        $this->query->multiply($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function divide($x)
    {
        $this->query->divide($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function mod($x)
    {
        $this->query->mod($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function bit($x)
    {
        $this->query->bit($x);
        return $this;
    }

}