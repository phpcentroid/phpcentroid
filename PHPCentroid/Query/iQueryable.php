<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 19/10/2016
 * Time: 7:05 πμ
 */
namespace PHPCentroid\Query;

interface iQueryable
{
    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function select($arg): self;

    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function also_select($arg): self;

    public function has_fields();

    public function has_filter();

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function group_by($expr): self;

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by($expr): self;

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function then_by($expr): self;

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function order_by_descending($expr): self;

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function then_by_descending($expr): self;

    /**
     * @param mixed $arg
     * @return $this
     */
    public function where($arg): self;

    /**
     * @param mixed $arg
     * @return $this
     */
    public function also($arg): self;

    /**
     * @param mixed $arg
     * @return $this
     */
    public function either($arg): self;

    public function prepare();

    /**
     * @param mixed $value
     * @return $this
     */
    public function equal($value): self;

    /**
     * @param mixed $value
     * @return $this
     */
    public function not_equal($value): self;

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_than($value): self;

    /**
     * @param mixed $value
     * @return $this
     */
    public function lower_or_equal($value): self;

    /**
     * @param mixed $value
     * @return $this
     */
    public function greater_than($value): self;

    /**
     * @param mixed $value
     * @return $this
     */
    public function greater_or_equal($value): self;

    public function get_day();

    public function get_month();

    public function get_year();

    public function get_seconds();

    public function get_minutes();

    public function get_hours();

    public function get_date();

    public function to_lower_case();

    public function to_upper_case();

    public function floor();

    public function ceil();

    public function trim();

    public function length();

    public function round(int $n) : self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function add($x): self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function subtract($x): self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function multiply($x): self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function divide($x): self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function mod($x): self;

    /**
     * @param mixed $x
     * @return $this
     */
    public function bit($x): self;

}