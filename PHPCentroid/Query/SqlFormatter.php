<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 10:00
 */

namespace PHPCentroid\Query;


use Closure;
use Error;
use PHPCentroid\Common\Args;
use ReflectionClass;
use ReflectionMethod;

class SqlFormatter implements iExpressionFormatter
{
    public array $settings = array('nameFormat' => '`1`');

    protected array $methods = array();

    public function __construct()
    {
        $reflectionClass = new ReflectionClass($this);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, function($x) {
            return current($x->getAttributes(SqlFormatterMethod::class)) !== FALSE;
        });
        $this->methods = array_map(function($method) {
            return array($method->getName(), $method);
        }, $methods);
    }


    #[SqlFormatterMethod]
    function count($arg): string {
        return "COUNT({$this->format($arg)})";
    }

    #[SqlFormatterMethod]
    function min($arg): string {
        return "MIN({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function max($arg): string {
        return "MAX({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function sum($arg): string {
        return "SUM({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function avg($arg): string {
        return "AVG({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function length($arg): string {
        return "LEN({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function startsWith($arg, $search): string {
        $s0 = $this->escape($arg);
        Args::check($search instanceof LiteralExpression,"Invalid pattern expression");
        Args::not_blank($search->value, "Pattern expression");
        $search->value = '^'.$search->value;
        $s1 = $this->escape($search);
        return "($s0 REGEXP $s1)";
    }

    #[SqlFormatterMethod]
    function endsWith($arg, $search): string {
        $s0 = $this->escape($arg);
        Args::check($search instanceof LiteralExpression,"Invalid pattern expression");
        Args::not_blank($search->value, "Pattern expression");
        $search->value = $search->value.'$';
        $s1 = $this->escape($search);
        return "($s0 REGEXP $s1)";
    }

    #[SqlFormatterMethod]
    function trim($arg): string {
        return "TRIM({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function concat(...$arg): string {
        return "CONCAT(".implode(', ', array_map(function($x) {
                return $this->escape($x);
            }, $arg)).")";
    }

    #[SqlFormatterMethod]
    function indexOf($arg0, $search): string {
        return "LOCATE({$this->escape($arg0)}, {$this->escape($search)})";
    }

    #[SqlFormatterMethod]
    function toLower($arg): string {
        return "LOWER({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function toUpper($arg): string {
        return "UPPER({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function contains($arg, $search): string {
        return "({$this->escape($arg)} REGEXP {$this->escape($search)})";
    }

    #[SqlFormatterMethod]
    function day($arg): string {
        return "DAY({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function month($arg): string {
        return "MONTH({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function year($arg): string {
        return "YEAR({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function hour($arg): string {
        return "HOUR({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function minute($arg): string {
        return "MINUTE({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function second($arg): string {
        return "SECOND({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function date($arg): string {
        return "DATE({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function floor($arg): string {
        return "FLOOR({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function ceiling($arg): string {
        return "CEILING({$this->escape($arg)})";
    }

    #[SqlFormatterMethod]
    function round($arg, $decimals = NULL): string {
        if (is_null($decimals)) {
            return "ROUND({$this->escape($arg)}, 0)";
        }
        return "ROUND({$this->escape($arg)}, {$this->escape($decimals)})";
    }

    #[SqlFormatterMethod]
    function bit($arg1, $arg2): string {
        return "({$this->escape($arg1)} & {$this->escape($arg2)})";
    }

    #[SqlFormatterMethod]
    function regex($arg, $pattern): string {
        return "({$this->escape($arg)} REGEXP {$this->escape($pattern)})";
    }

    #[SqlFormatterMethod]
    function text($arg, $pattern): string {
        return "({$this->escape($arg)} REGEXP {$this->escape($pattern)})";
    }

    #[SqlFormatterMethod]
    function substring($arg, $pos, $length = NULL): string {
        if (is_null($length)) {
            return "SUBSTRING({$this->escape($arg)}, {$this->escape($pos)} + 1)";
        }
        return "SUBSTRING({$this->escape($arg)}, {$this->escape($pos)} + 1, {$this->escape($length)})";
    }

    /**
     * @param ComparisonExpression $expr
     * @return string
     */
    protected function formatComparison(ComparisonExpression $expr): string {
        $left = $this->format($expr->left);
        $right = $this->format($expr->right);
        if (preg_match('/^null$/i',$right)) {
            switch ($expr->operator) {
                case 'eq':
                    return "$left IS NULL";
                case 'ne':
                    return "NOT $left IS NULL";
            }
        }
        switch ($expr->operator) {
            case 'eq':
                return "($left = $right)";
            case 'ne':
                return "($left <> $right)";
            case 'gt':
                return "($left > $right)";
            case 'lt':
                return "($left < $right)";
            case 'ge':
                return "($left >= $right)";
            case 'le':
                return "($left <= $right)";
            case 'in':
                Args::check(is_array($right),"Right operand must be an array");
                if (count($right) == 1) {
                    return $this->formatComparison(new ComparisonExpression($left,'eq',$right[0]));
                }
                return "($left IN ($right))";
            case 'nin':
                Args::check(is_array($right),"Right operand must be an array");
                if (count($right) == 1) {
                    return $this->formatComparison(new ComparisonExpression($left,'ne',$right[0]));
                }
                return "($left IN ($right))";
            default:
                throw new Error('Unsupported comparison operator');
        }
    }

    /**
     * @param ArithmeticExpression $expr
     * @return string
     */
    protected function formatArithmetic(ArithmeticExpression $expr): string {
        $left = $this->format($expr->left);
        $right = $this->format($expr->right);
        switch ($expr->operator) {
            case 'add':
                return "($left + $right)";
            case 'mul':
                return "($left * $right)";
            case 'div':
                return "($left / $right)";
            case 'sub':
                return "($left - $right)";
            case 'mod':
                return "($left % $right)";
        }
        throw new Error("Unsupported arithmetic operator");
    }

    protected function formatLimitSelect($expr): string {
        $sql = $this->formatSelect($expr);
        if (array_key_exists('top', $expr->params) && is_numeric($expr->params['top'])) {
            $top = intval($expr->params['top']);
            if ($top<0) {
                return $sql;
            }
            if (array_key_exists('skip', $expr->params) && is_numeric($expr->params['skip'])) {
                $skip = intval($expr->params['skip']);
                if ($skip<=0) {
                    return "$sql LIMIT $top";
                }
                else {
                    return "$sql LIMIT $top, $skip";
                }
            }
        }
        return $sql;
    }

    public function escape(mixed $value): string {
        if ($value instanceof DataQueryExpression) {
            return $this->format($value);
        }
        return DataQueryExpression::escape($value);
    }

    public function escapeName(string $name): string {
        return preg_replace('/(\w+)/', $name, $this->settings['nameFormat']);
    }

    /**
     * @param QueryExpression $expression
     * @return string
     */
    protected function formatSelect(QueryExpression $expression): string {
        Args::check(array_key_exists('select', $expression->params) && is_array($expression->params['select']), "Invalid select expression. Expected array");
        Args::not_empty($expression->params['select'], "Select arguments");
        Args::check(array_key_exists('entity', $expression->params) && $expression->params['entity'] instanceof EntityExpression, "Invalid entity expression. Expected object");
        /**
         * @var EntityExpression $entity
         */
        $entity = $expression->params['entity'];

        /**
         * @param SelectableExpression $x
         * @return string
         */
        $map = function(SelectableExpression $x) {
            if (!isset($x->alias)) {
                return $this->format($x);
            }
            return $this->format($x).' AS '.$this->escapeName($x->alias);
        };
        $from = $this->format($entity);
        $select = implode(', ', array_map($map, $expression->params['select']));
        if ($expression->params['fixed']) {
            return "SELECT * FROM (SELECT $select) $from";
        }
        //build SQL statement
        //1. select statement
        if ($expression->params['distinct']) {
            $sql = "SELECT DISTINCT $select FROM $from";
        }
        else {
            $sql = "SELECT $select FROM $from";
        }
        //2. where statement
        if ($expression->has_filter()) {
            $sql .= $this->formatWhere($expression);
        }
        //3. group by statement
        if ($expression->has_groups()) {
            $sql .= $this->formatGroupBy($expression);
        }
        //4. order by statement
        if ($expression->has_orders()) {
            $sql .= $this->formatOrderBy($expression);
        }
        return $sql;
    }

    protected function formatUpdate(QueryExpression $expression): mixed {
        throw new Error("Not implemented");
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function formatInsert(QueryExpression $expression): mixed {
        throw new Error("Not implemented");
    }

    protected function formatOrderBy(mixed $expression): string {
        Args::check($expression instanceof QueryExpression, 'Invalid argument. Expected query expression');
        if (!array_key_exists('orderby', $expression->params)) {
            return '';
        }
        /**
         * @var MemberListExpression $orders
         */
        $orders = $expression->params['orderby'];
        Args::check($orders instanceof MemberListExpression, 'Invalid order expression. Expected member list');
        if ($orders->count() == 0) {
            return '';
        }
        $map = function (SelectableExpression $expr) {
            return $this->format($expr).($expr->order=='desc' ? ' DESC' : ' ASC');
        };
        return ' ORDER BY '.implode(', ', array_map($map, $orders->getArrayCopy()));
    }

    protected function formatGroupBy(mixed $expression): string {
        Args::check($expression instanceof QueryExpression, 'Invalid argument. Expected query expression');
        if (!array_key_exists('groupby', $expression->params)) {
            return '';
        }
        $groups = $expression->params['groupby'];
        Args::check($groups instanceof MemberListExpression, 'Invalid group by expression. Expected member list');
        if ($groups->count() == 0) {
            return '';
        }
        $map = function (SelectableExpression $expr) {
            return $this->format($expr);
        };
        return ' GROUP BY '.implode(', ', array_map($map, $groups->getArrayCopy()));
    }

    protected function formatDelete(QueryExpression $expression): mixed {
        throw new Error("Not implemented");
    }

    /**
     * @param QueryExpression $expression
     * @return string
     */
    protected function formatWhere(QueryExpression $expression): string {
        $expr = $expression->get_filter();
        if (is_null($expr)) {
            return '';
        }
        if ($expr instanceof ComparisonExpression) {
            return ' WHERE '.$this->formatComparison($expr);
        }
        else if ($expr instanceof LogicalExpression) {
            return ' WHERE '.$this->formatLogical($expr);
        }
        throw new Error("Invalid filter expression. Expected a logical or comparison expression");
    }

    protected function formatLogical(mixed $expr): string {

        Args::check($expr instanceof LogicalExpression, 'Invalid argument. Expected logical expression');
        $args = array_map(function($x) {
            return $this->format($x);
        }, $expr->args);
        switch ($expr->operator) {
            case 'or':
                return '('.join(' OR ', $args).')';
            case 'and':
                return '('.join(' AND ', $args).')';
            case 'nor':
                return 'NOT ('.join(' OR ', $args).')';
            case 'not':
                return 'NOT ('.join(' AND ', $args).')';
        }
        throw new Error('Unsupported logical operator');
    }

    /**
     * @param MethodCallExpression $expr
     * @return mixed
     */
    protected function formatMethod(MethodCallExpression $expr): mixed {
        $args = array_map(function($arg) {
           return $this->format($arg);
        }, $expr->args);
        if (array_key_exists($expr->method, $this->methods)) {
            return call_user_func_array($this->methods[$expr->method], $expr->args);
        }
        if (count($args)==0) {
            return $expr->method.'()';
        }
        return $expr->method.'('.implode(', ', $args).')';
    }

    public function resolveEntity(Closure $closure): void {
        $this->entityResolver = $closure;
    }

    public function resolveMember(Closure $closure): void {
        $this->entityResolver = $closure;
    }

    protected function formatMember(mixed $expr): string {

        if ($this->memberResolver instanceof Closure) {
            $member = $this->memberResolver->call($this, $expr->name, $expr->entity);
            Args::string($member, 'Member');
            Args::not_blank($member, 'Member');
            if (is_null($expr->entity)) {
                return $this->escapeName($member);
            }
            else {
                return $this->formatEntity($expr->entity).'.'.$this->escapeName($member);
            }
        }
        else {
            if (is_null($expr->entity))
                return $this->escapeName($expr->name);
            else
                return $this->formatEntity($expr->entity).'.'.$this->escapeName($expr->name);
        }
    }

    protected function formatEntity($expr): string {
        Args::check(is_string($expr) || ($expr instanceof EntityExpression), 'Invalid argument. Expected string or an entity expression');
        if (is_string($expr)) {
            $entity = $expr;
        }
        else {
            $entity = $expr->name;
        }
        if ($this->entityResolver instanceof Closure) {
            $entity = $this->entityResolver->call($this, $entity);
            Args::string($entity, 'Entity');
            Args::not_blank($entity, 'Entity');
        }
        return $this->escapeName($entity);
    }

    /**
     * @param mixed $expr
     * @param string $format
     * @return mixed
     */
    public function format($expr, $format = NULL): mixed {
        if ($expr instanceof EntityExpression) {
            return $this->formatEntity($expr);
        }
        else if ($expr instanceof MemberExpression) {
            return $this->formatMember($expr);
        }
        else if ($expr instanceof ComparisonExpression) {
            return $this->formatComparison($expr);
        }
        else if ($expr instanceof LogicalExpression) {
            return $this->formatLogical($expr);
        }
        else if ($expr instanceof QueryExpression) {
            return $this->formatSelect($expr);
        }
        else if ($expr instanceof MethodCallExpression) {
            return $this->formatMethod($expr);
        }
        else if ($expr instanceof LiteralExpression) {
            return $this->escape($expr->value);
        }
        return NULL;
    }

}