<?php /** @noinspection PhpUnused */

namespace PHPCentroid\Data;
use PHPCentroid\Query\iQueryable;
use PHPCentroid\Query\QueryExpression;
use PHPCentroid\Query\SelectableExpression;

class DataQueryable implements iQueryable
{
    /**
     * @var DataModel
     */
    private DataModel $model;
    /**
     * @var QueryExpression
     */
    private QueryExpression $query;
    /**
     * DataQueryable constructor.
     * @param DataModel $model
     */
    public function __construct(DataModel $model)
    {
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
    public function also_select($arg): iQueryable
    {
        $this->query->also_select($arg);
        return $this;
    }

    public function has_fields(): bool|int
    {
        return $this->query->has_fields();
    }

    public function has_filter(): bool
    {
        return $this->query->has_filter();
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function group_by($expr): iQueryable
    {
        $this->query->group_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by($expr): iQueryable
    {
        $this->query->order_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function then_by($expr): iQueryable
    {
        $this->query->then_by($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by_descending($expr): iQueryable
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
    public function either($arg): iQueryable
    {
        $this->query->either($arg);
        return $this;
    }
    /**
     * @return $this
     */
    public function prepare(): iQueryable
    {
        $this->query->prepare();
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function equal($value): iQueryable
    {
        $this->query->equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function not_equal($value): iQueryable
    {
        $this->query->not_equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_than($value): iQueryable
    {
        $this->query->lower_than($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_or_equal($value): iQueryable
    {
        $this->query->lower_or_equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greater_than($value): iQueryable
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

    public function get_month(): iQueryable
    {
        $this->query->get_month();
        return $this;
    }

    public function get_year(): iQueryable
    {
        $this->query->get_year();
        return $this;
    }

    public function get_seconds(): iQueryable
    {
        $this->query->get_seconds();
        return $this;
    }

    public function get_minutes(): iQueryable
    {
        $this->query->get_minutes();
        return $this;
    }

    public function get_hours(): iQueryable
    {
        $this->query->get_hours();
        return $this;
    }

    public function get_date(): iQueryable
    {
        $this->query->get_date();
        return $this;
    }

    public function to_lower_case(): iQueryable
    {
        $this->query->to_lower_case();
        return $this;
    }

    public function to_upper_case(): iQueryable
    {
        $this->query->to_upper_case();
        return $this;
    }

    public function floor(): iQueryable
    {
        $this->query->floor();
        return $this;
    }

    public function ceil(): iQueryable
    {
        $this->query->ceil();
        return $this;
    }

    public function trim(): iQueryable
    {
        $this->query->trim();
        return $this;
    }

    /**
     * @return $this
     */
    public function length(): iQueryable
    {
        $this->query->length();
        return $this;
    }

    /**
     * @param integer $n
     * @return $this
     */
    public function round(int $n): iQueryable
    {
        $this->query->round($n);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function add($x): iQueryable
    {
        $this->query->add($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function subtract($x): iQueryable
    {
        $this->query->subtract($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function multiply($x): iQueryable
    {
        $this->query->multiply($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function divide($x): iQueryable
    {
        $this->query->divide($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function mod($x): iQueryable
    {
        $this->query->mod($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function bit($x): iQueryable
    {
        $this->query->bit($x);
        return $this;
    }

}