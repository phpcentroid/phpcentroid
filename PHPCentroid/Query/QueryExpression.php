<?php /** @noinspection DuplicatedCode */

/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 10:14
 */

namespace PHPCentroid\Query;


use PHPCentroid\Common\Args;
use UnexpectedValueException;

class QueryExpression implements iQueryable
{

    public $params = array('select' => array(), 'distinct' => FALSE, 'fixed' => FALSE);

    const JOIN_DIRECTION_LEFT = 'left';
    const JOIN_DIRECTION_RIGHT = 'right';
    const JOIN_DIRECTION_INNER = 'inner';

    /**
     * @var SelectableExpression
     */
    private $__left;
    /**
     * @var string
     */
    private $__lop;
    /**
     * @var string
     */
    private $__prepared_lop;
    /**
     * @var JoinExpression
     */
    private $__join;

    public function __construct($entity = NULL)
    {
        if (!is_null($entity)) {
            $this->from($entity);
        }
    }

    public static function create($entity = NULL): QueryExpression
    {
        return new QueryExpression($entity);
    }

    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function select($arg): iQueryable
    {
        $arguments = func_get_args();
        $this->params['select'] = array();
        foreach ($arguments as $argument) {
            $this->do_select_string($argument);
        }
        return $this;
    }

    private function do_select_string($argument) {
        Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid select argument. Expected string or a valid query expression");
        if (is_string($argument)) {
            $this->params['select'][] = new MemberExpression($argument);
        }
        else if ($argument instanceof SelectableExpression) {
            $this->params['select'][] = $argument;
        }
    }

    /**
     * @param string|SelectableExpression $arg,...
     * @return $this
     */
    public function alsoSelect($arg): iQueryable
    {
        $arguments = func_get_args();
        if (!array_key_exists('select', $this->params)) {
            $this->params['select'] = array();
        }
        foreach ($arguments as $argument) {
            $this->do_select_string($argument);
        }
        return $this;
    }

    public function has_fields() {
        if (array_key_exists('select',$this->params)) {
            return count($this->params['select']);
        }
        return false;
    }

    public function has_filter(): bool
    {
        return array_key_exists('prepared',$this->params) || array_key_exists('filter',$this->params);
    }

    public function has_orders(): bool
    {
        return array_key_exists('orderby',$this->params);
    }

    public function has_groups(): bool
    {
        return array_key_exists('groupby',$this->params);
    }

    public function from($entity): iQueryable
    {
        Args::check(is_string($entity) || ($entity instanceof EntityExpression), "Invalid entity argument. Expected string or a valid entity expression");
        if (is_string($entity)) {
            $this->params['entity'] = new EntityExpression($entity);
        }
        else if ($entity instanceof EntityExpression) {
            $this->params['entity'] = $entity;
        }
        return $this;
    }

    /**
     * @param bool $value
     */
    public function distinct(bool $value = TRUE) {
        if ($value) {
            $this->params['distinct'] = TRUE;
        }
        else {
            $this->params['distinct'] = FALSE;
        }
    }

    /**
     * @param bool $value
     */
    public function fixed(bool $value = TRUE) {
        if ($value) {
            $this->params['fixed'] = TRUE;
        }
        else {
            $this->params['fixed'] = FALSE;
        }
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function groupBy($expr): iQueryable
    {
        $arguments = func_get_args();
        $this->params['groupby'] = new MemberListExpression(array());
        foreach ($arguments as $argument) {
            Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid group by argument. Expected string or a valid selectable expression");
            if (is_string($expr)) {
                $this->params['groupby']->append(new MemberExpression($argument));
            }
            else {
                $expr->alias = NULL;
                $this->params['orderby']->append($argument);
            }
        }
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function orderBy($expr): iQueryable {
        $arguments = func_get_args();
        $this->params['orderby'] = new MemberListExpression(array());
        foreach ($arguments as $argument) {
            Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid order argument. Expected string or a valid selectable expression");
            if (is_string($expr)) {
                $this->params['orderby']->append(new MemberExpression($argument));
            }
            else {
                $expr->alias = NULL;
                $this->params['orderby']->append($argument);
            }
        }
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function thenBy($expr): iQueryable {
        Args::check(array_key_exists('orderby', $this->params), "Order expression has not been yet initialized.");
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid order argument. Expected string or a valid selectable expression");
            if (is_string($expr)) {
                $this->params['orderby']->append(new MemberExpression($expr));
            }
            else {
                $expr->alias = NULL;
                $expr->order = NULL;
                $this->params['orderby']->append($expr);
            }
        }
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function orderByDescending($expr): iQueryable
    {
        $arguments = func_get_args();
        $this->params['orderby'] = new MemberListExpression(array());
        foreach ($arguments as $argument) {
            Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid order argument. Expected string or a valid selectable expression");
            if (is_string($expr)) {
                $this->params['orderby']->append(MemberExpression::create($expr)->orderBy(SelectableExpression::ORDER_DESCENDING));
            }
            else {
                $expr->alias = NULL;
                $expr->orderBy(SelectableExpression::ORDER_DESCENDING);
                $this->params['orderby']->append($expr);
            }
        }
        return $this;
    }

    /**
     * @param SelectableExpression|string $expr,...
     * @return $this
     */
    public function thenByDescending($expr): iQueryable
    {
        Args::check(array_key_exists('orderby', $this->params), "Order expression has not been yet initialized.");
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            Args::check(is_string($argument) || ($argument instanceof SelectableExpression), "Invalid order argument. Expected string or a valid selectable expression");
            if (is_string($expr)) {
                $this->params['orderby']->append(MemberExpression::create($expr)->orderBy(SelectableExpression::ORDER_DESCENDING));
            }
            else {
                $expr->alias = NULL;
                $expr->orderBy(SelectableExpression::ORDER_DESCENDING);
                $this->params['orderby']->append($expr);
            }
        }
        return $this;
    }

    /**
     * @param ComparisonExpression $comparison
     */
    private function __append_comparison(ComparisonExpression $comparison) {
        if (array_key_exists('filter', $this->params)) {
            if (is_null($this->__lop)) {
                $this->__lop = LogicalExpression::OPERATOR_AND;
            }
            $expr = $this->params['filter'];
            if ($expr instanceof ComparisonExpression) {
                $this->params['filter'] = new LogicalExpression($this->__lop, array(
                    $expr,
                    $comparison
                ));
            }
            else if ($expr instanceof LogicalExpression) {
                if ($expr->operator === $this->__lop) {
                    $this->params['filter']->args[] = $comparison;
                }
                else {
                    $this->params['filter'] = new LogicalExpression($this->__lop, array(
                        $expr,
                        $comparison
                    ));
                }
            }
        }
        else {
            $this->params['filter'] = $comparison;
        }
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function where($arg): iQueryable {
        Args::not_null($arg,'Filter attribute');
        Args::check(is_string($arg) || ($arg instanceof SelectableExpression),'Invalid argument. Expected string or a valid selectable expression');
        if (is_string($arg)) {
            $this->__left = new MemberExpression($arg);
        }
        else if ($arg instanceof SelectableExpression) {
            $this->__left = $arg;
        }
        //destroy filter
        if (array_key_exists('filter', $this->params)) {
            unset($this->params['filter']);
        }
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function also($arg): iQueryable {
        Args::not_null($arg,'Filter attribute');
        Args::check(is_string($arg) || ($arg instanceof SelectableExpression),'Invalid filter attribute. Expected string or a valid selectable expression');
        $this->__lop = 'and';
        if (is_string($arg)) {
            $this->__left = new MemberExpression($arg);
        }
        else {
            $this->__left = $arg;
        }
        return $this;
    }

    /**
     * @param mixed $arg
     * @return $this
     */
    public function either($arg): iQueryable
    {
        Args::not_null($arg,'Filter attribute');
        Args::check(is_string($arg) || ($arg instanceof SelectableExpression),'Invalid filter attribute. Expected string or a valid selectable expression');
        $this->__lop = 'or';
        if (is_string($arg)) {
            $this->__left = new MemberExpression($arg);
        }
        else {
            $this->__left = $arg;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function prepare(): QueryExpression
    {
        if (is_null($this->params['filter']))
            return $this;
        if (is_null($this->__prepared_lop)) {
            $this->__prepared_lop = LogicalExpression::OPERATOR_AND;
        }
        if (isset($this->params['prepared'])) {
            $prepared = $this->params['prepared'];
            if ($prepared instanceof ComparisonExpression) {
                $this->params['prepared'] = new LogicalExpression($this->__prepared_lop, array(
                    $prepared,
                    $this->params['filter']
                ));
                unset($this->params['filter']);
            }
            else if ($prepared instanceof LogicalExpression) {
                if ($prepared->operator == $this->__prepared_lop) {
                    $prepared->args[] = $this->params['filter'];
                }
                else {
                    $this->params['prepared'] = new LogicalExpression($this->__prepared_lop, array(
                        $prepared,
                        $this->params['filter']
                    ));
                }
                unset($this->params['filter']);
            }
            else {
                throw new UnexpectedValueException('Unsupported prepared expression');
            }
        }
        else {
            $this->params['prepared'] =$this->params['filter'];
            unset($this->params['filter']);
        }
        return $this;
    }

    public function get_filter() {
        if (isset($this->params['prepared'])) {
            if (isset($this->params['filter'])) {
                $lop = $this->__prepared_lop;
                if (is_null($lop)) {
                    $lop = LogicalExpression::OPERATOR_AND;
                }
                return new LogicalExpression($lop, array(
                    $this->params['prepared'],
                    $this->params['filter']
                ));
            }
            else {
                return $this->params['prepared'];
            }
        }
        else if (isset($this->params['filter'])) {
            return $this->params['filter'];
        }
        return NULL;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function equal($value): iQueryable
    {
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_EQUAL, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function notEqual($value): iQueryable
    {
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_NOT_EQUAL, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lowerThan($value): iQueryable
    {
        Args::check(!is_null($value), "The right operand may cannot be null");
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_LOWER, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function lowerOrEqual($value): iQueryable
    {
        Args::check(!is_null($value), "The right operand may cannot be null");
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_LOWER_OR_EQUAL, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greaterThan($value): iQueryable
    {
        Args::check(!is_null($value), "The right operand may cannot be null");
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_GREATER, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function greaterOrEqual($value): iQueryable
    {
        Args::check(!is_null($value), "The right operand may cannot be null");
        $this->__append_comparison(new ComparisonExpression($this->__left,  ComparisonExpression::OPERATOR_GREATER_OR_EQUAL, $value));
        $this->__left = NULL;
        return $this;
    }

    /**
     * @param string $method
     * @param ?array $add_args
     * @return $this
     */
    private function wrap_left_operand_with_method(string $method, array $add_args = NULL): iQueryable {
        Args::not_null($this->__left, "Left operand");
        Args::check($this->__left instanceof SelectableExpression, "Left operand must be a selectable expression");
        $this->__left = new MethodCallExpression($method,array($this->__left));
        if (is_array($add_args)) {
            foreach ($add_args as $add_arg) {
                $this->__left->args[] = $add_arg;
            }
        }
        return $this;
    }

    public function getDay(): QueryExpression
    {
        return $this->wrap_left_operand_with_method('day');
    }

    public function getMonth() {
        return $this->wrap_left_operand_with_method('month');
    }

    public function getYear() {
        return $this->wrap_left_operand_with_method('year');
    }

    public function getSeconds() {
        return $this->wrap_left_operand_with_method('second');
    }

    public function getMinutes(): QueryExpression
    {
        return $this->wrap_left_operand_with_method('minute');
    }


    public function getHours(): QueryExpression
    {
        return $this->wrap_left_operand_with_method('hour');
    }

    public function getDate(): QueryExpression
    {
        return $this->wrap_left_operand_with_method('date');
    }

    public function toLowerCase() {
        return $this->wrap_left_operand_with_method('tolower');
    }

    public function toUpperCase() {
        return $this->wrap_left_operand_with_method('toupper');
    }

    public function floor() {
        return $this->wrap_left_operand_with_method('floor');
    }

    public function ceil() {
        return $this->wrap_left_operand_with_method('ceiling');
    }

    public function trim() {
        return $this->wrap_left_operand_with_method('trim');
    }

    /**
     * @return $this
     */
    public function length() {
        return $this->wrap_left_operand_with_method('length');
    }

    /**
     * @param integer $n
     * @return $this
     */
    public function round(int $n = 4): iQueryable
    {
        return $this->wrap_left_operand_with_method('round', array(new LiteralExpression($n)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function add($x): iQueryable
    {
        Args::check(is_numeric($x), "Invalid argument. Expected numeric");
        return $this->wrap_left_operand_with_method('add', array(new LiteralExpression($x)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function subtract($x): iQueryable
    {
        Args::check(is_numeric($x), "Invalid argument. Expected numeric");
        return $this->wrap_left_operand_with_method('sub', array(new LiteralExpression($x)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function multiply($x): iQueryable
    {
        Args::check(is_numeric($x), "Invalid argument. Expected numeric");
        return $this->wrap_left_operand_with_method('mul', array(new LiteralExpression($x)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function divide($x): iQueryable
    {
        Args::check(is_numeric($x) && $x!=0, "Invalid argument. Expected numeric other than zero");
        return $this->wrap_left_operand_with_method('div', array(new LiteralExpression($x)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function mod($x): iQueryable
    {
        Args::check(is_numeric($x), "Invalid argument. Expected numeric");
        return $this->wrap_left_operand_with_method('mod', array(new LiteralExpression($x)));
    }

    /**
     * @param mixed $x
     * @return $this
     */
    public function bit($x): iQueryable
    {
        Args::check(is_numeric($x), "Invalid argument. Expected numeric");
        return $this->wrap_left_operand_with_method('bit', array(new LiteralExpression($x)));
    }

    /**
     * @param string|EntityExpression $entity
     * @param string $direction
     * @return $this
     */
    public function join($entity, string $direction = 'inner'): iQueryable {
        Args::check(is_string($entity) || ($entity instanceof EntityExpression), "Invalid entity argument. Expected string or a valid entity expression");
        $this->__join = new JoinExpression($entity,$direction);
        return $this;
    }

    /**
     * @param ComparisonExpression|LogicalExpression $expr
     * @return $this
     */
    public function with($expr): iQueryable {
        Args::check($this->__join instanceof JoinExpression, "Join entity expression has not been initialized.");
        $this->__join->with($expr);
        if (isset($this->params['expand'])) {
            $this->params['expand'] = array();
        }
        $this->params['expand'][] = $this->__join;
        return $this;
    }


}