<?php /** @noinspection PhpUnused */

namespace PHPCentroid\Data;
use PHPCentroid\Query\iQueryable;
use PHPCentroid\Query\QueryExpression;
use PHPCentroid\Query\SelectableExpression;

class DataQueryable implements iQueryable
{
    /**
     * @var DataModelBase
     */
    private DataModelBase $model;
    /**
     * @var QueryExpression
     */
    private QueryExpression $query;
    /**
     * DataQueryable constructor.
     * @param DataModel $model
     */
    public function __construct(DataModelBase $model)
    {
        $this->model = $model;
        $this->query = new QueryExpression($this->model->getView());
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
    public function alsoSelect($arg): iQueryable
    {
        $this->query->alsoSelect($arg);
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
    public function groupBy($expr): iQueryable
    {
        $this->query->groupBy($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function orderBy($expr): iQueryable
    {
        $this->query->orderBy($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function thenBy($expr): iQueryable
    {
        $this->query->thenBy($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function orderByDescending($expr): iQueryable
    {
        $this->query->orderByDescending($expr);
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function thenByDescending($expr): DataQueryable
    {
        $this->query->thenByDescending($expr);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function where(mixed $arg): DataQueryable
    {
        $this->query->where($arg);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function also(mixed $arg): DataQueryable
    {
        $this->query->also($arg);
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function either(mixed $arg): iQueryable
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
    public function equal(mixed $value): iQueryable
    {
        $this->query->equal($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function notEqual(mixed $value): iQueryable
    {
        $this->query->notEqual($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lowerThan(mixed $value): iQueryable
    {
        $this->query->lowerThan($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lowerOrEqual(mixed $value): iQueryable
    {
        $this->query->lowerOrEqual($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greaterThan(mixed $value): iQueryable
    {
        $this->query->greaterThan($value);
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greaterOrEqual(mixed $value): DataQueryable
    {
        $this->query->greaterOrEqual($value);
        return $this;
    }

    public function getDay(): DataQueryable
    {
        $this->query->getDay();
        return $this;
    }

    public function getMonth(): iQueryable
    {
        $this->query->getMonth();
        return $this;
    }

    public function getYear(): iQueryable
    {
        $this->query->getYear();
        return $this;
    }

    public function getSeconds(): iQueryable
    {
        $this->query->getSeconds();
        return $this;
    }

    public function getMinutes(): iQueryable
    {
        $this->query->getMinutes();
        return $this;
    }

    public function getHours(): iQueryable
    {
        $this->query->getHours();
        return $this;
    }

    public function getDate(): iQueryable
    {
        $this->query->getDate();
        return $this;
    }

    public function toLowerCase(): iQueryable
    {
        $this->query->toLowerCase();
        return $this;
    }

    public function toUpperCase(): iQueryable
    {
        $this->query->toUpperCase();
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
    public function add(mixed $x): iQueryable
    {
        $this->query->add($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function subtract(mixed $x): iQueryable
    {
        $this->query->subtract($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function multiply(mixed $x): iQueryable
    {
        $this->query->multiply($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function divide(mixed $x): iQueryable
    {
        $this->query->divide($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function mod(mixed $x): iQueryable
    {
        $this->query->mod($x);
        return $this;
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function bit(mixed $x): iQueryable
    {
        $this->query->bit($x);
        return $this;
    }

}