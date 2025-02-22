<?php
/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 07/10/16
 * Time: 10:00
 */

namespace PHPCentroid\Query;


use Closure;
use PHPCentroid\Common\Args;

class SqlFormatter implements iExpressionFormatter
{
    public $settings = array('nameFormat' => '`1`');

    private $methods = array();
    /**
     * @var Closure
     */
    private $member_resolver;
    /**
     * @var Closure
     */
    private $entity_resolver;

    public function __construct()
    {
        //count()
        $this->method('count', function($p0) {
            return 'COUNT('.$this->format($p0).')';
        })//max()
            ->method('max',function($p0) {
            return 'MAX('.$this->format($p0).')';
        })//min()
            ->method('min',function($p0) {
            return 'MIN('.$this->format($p0).')';
        })//avg()
            ->method('avg',function($p0) {
            return 'AVG('.$this->format($p0).')';
        })//sum()
            ->method('sum',function($p0) {
            return 'SUM('.$this->format($p0).')';
        })//regex()
            ->method('regex', function($p0, $p1) {
            $s0 = $this->format($p0);
            Args::check($p1 instanceof LiteralExpression,"Invalid pattern expression");
            Args::not_blank($p1->value, "Pattern expression");
            $s1 = $this->escape($p1);
            return "($s0 REGEXP $s1)";
        })//length()
            ->method('length',function($p0) {
            return 'LEN('.$this->format($p0).')';
        })//startswith()
        ->method('startswith', function($p0, $p1) {
            $s0 = $this->format($p0);
            Args::check($p1 instanceof LiteralExpression,"Invalid pattern expression");
            Args::not_blank($p1->value, "Pattern expression");
            $p1->value = '^'.$p1->value;
            $s1 = $this->escape($p1);
            return "($s0 REGEXP $s1)";
        })//endswith()
        ->method('endswith', function($p0, $p1) {
            $s0 = $this->format($p0);
            Args::check($p1 instanceof LiteralExpression,"Invalid pattern expression");
            Args::not_blank($p1->value, "Pattern expression");
            $p1->value = $p1->value.'$';
            $s1 = $this->escape($p1);
            return "($s0 REGEXP $s1)";
        })//trim()
        ->method('trim', function($p0) {
            return 'TRIM('.$this->format($p0).')';
        })//concat()
        ->method('concat', function($p0, $p1) {
            $s0 = $this->format($p0);
            $s1 = $this->format($p1);
            return "CONCAT($s0, $s1)";
        })//indexof()
        ->method('indexof', function($p0, $p1) {
            $s0 = $this->format($p0);
            $s1 = $this->format($p1);
            return "LOCATE($s0, $s1)";
        })//substring()
        ->method('substring', function($p0, $pos, $length = NULL) {
            $s0 = $this->format($p0);
            Args::check(is_int($pos->value) && ($pos->value>=0),"Substring position must be a valid non-negative integer");
            $s1 = $this->escape($pos->value + 1);
            if (is_null($length)) {
                return "SUBSTRING($s0, $s1)";
            }
            else {
                Args::check(is_int($length->value) && ($length->value>0), "Substring length must be a valid positive integer");
                $s2 = $this->escape($length->value);
                return "SUBSTRING($s0, $s1, $s2)";
            }
        })//tolower()
        ->method('tolower', function($p0) {
            return 'LOWER('.$this->format($p0).')';
        })//toupper()
        ->method('toupper', function($p0) {
            return 'UPPER('.$this->format($p0).')';
        })//contains()
        ->method('contains', function($p0, $p1) {
            $s0 = $this->format($p0);
            Args::check($p1 instanceof LiteralExpression,"Invalid pattern expression");
            Args::not_blank($p1->value, "Pattern expression");
            $s1 = $this->escape($p1);
            return "($s0 REGEXP $s1)";
        })//text()
        ->method('text', function($p0, $p1) {
            $s0 = $this->format($p0);
            Args::check($p1 instanceof LiteralExpression,"Invalid pattern expression");
            Args::not_blank($p1->value, "Pattern expression");
            $s1 = $this->escape($p1);
            return "($s0 REGEXP $s1)";
        })//day()
        ->method('day', function($p0) {
            return 'DAY('.$this->format($p0).')';
        })//month()
        ->method('month', function($p0) {
            return 'MONTH('.$this->format($p0).')';
        })//year()
        ->method('year', function($p0) {
            return 'YEAR('.$this->format($p0).')';
        })//hour()
        ->method('hour', function($p0) {
            return 'HOUR('.$this->format($p0).')';
        })//minute()
        ->method('minute', function($p0) {
            return 'MINUTE('.$this->format($p0).')';
        })//second()
        ->method('second', function($p0) {
            return 'SECOND('.$this->format($p0).')';
        })//date()
        ->method('date', function($p0) {
            return 'DATE('.$this->format($p0).')';
        })//floor()
        ->method('floor', function($p0) {
            return 'FLOOR('.$this->format($p0).')';
        })//ceiling()
        ->method('ceiling', function($p0) {
            return 'CEILING('.$this->format($p0).')';
        })//ceiling()
        ->method('ceiling', function($p0) {
            return 'CEILING('.$this->format($p0).')';
        })//round()
        ->method('round', function($p0, $decimals = NULL) {
            $s0 = $this->format($p0);
            if (is_null($decimals)) {
                return "ROUND($s0, 0)";
            }
            else {
                $s1 = $this->format($decimals);
                return "ROUND($s0, $s1)";
            }
        })//bit()
        ->method('bit', function($p0, $p1) {
            $s0 = $this->format($p0);
            $s1 = $this->format($p1);
            return "($s0 & $s1)";
        });
    }

    /**
     * @param string $name
     * @param Closure $closure
     * @return $this;
     */
    public function method($name, $closure) {
        Args::not_blank($name, 'Method name');
        Args::check($closure instanceof Closure, "Method must a valid closure");
        $this->methods[$name] = $closure;
        return $this;
    }

    /**
     * @param ComparisonExpression $expr
     * @return string
     */
    protected function format_comparison($expr) {
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
                    return $this->format_comparison(new ComparisonExpression($left,'eq',$right[0]));
                }
                return "($left IN ($right))";
            case 'nin':
                Args::check(is_array($right),"Right operand must be an array");
                if (count($right) == 1) {
                    return $this->format_comparison(new ComparisonExpression($left,'ne',$right[0]));
                }
                return "($left IN ($right))";
            default:
                throw new \Error('Unsupported comparison operator');
        }
    }

    /**
     * @param ArithmeticExpression $expr
     * @return string
     */
    protected function format_arithmetic($expr) {
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
        throw new \Error("Unsupported arithmetic operator");
    }

    protected function format_limit_select($expr): string {
        $sql = $this->format_select($expr);
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

    public function escape($value) {
        return DataQueryExpression::escape($value);
    }

    public function escape_name($name) {
        if (is_string($name)) {
            return preg_replace('/(\w+)/', $name, $this->settings['nameFormat']);
        }
        return $name;
    }

    /**
     * @param QueryExpression $expression
     * @return string
     */
    protected function format_select($expression) {
        Args::check($expression instanceof QueryExpression, "Invalid argument. Expected QueryExpression");
        Args::check(array_key_exists('select', $expression->params) && is_array($expression->params['select']), "Invalid select expression. Expected array");
        Args::not_empty($expression->params['select'], "Select arguments");
        Args::check(array_key_exists('entity', $expression->params) && $expression->params['entity'] instanceof EntityExpression, "Invalid entity expression. Expected object");
        /**
         * @var EntityExpression
         */
        $entity = $expression->params['entity'];

        /**
         * @param SelectableExpression $x
         * @return string
         */
        $map = function($x) {
            if (is_null($x->alias)) {
                return $this->format($x);
            }
            return $this->format($x).' AS '.$this->escape_name($x->alias);
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
            $sql .= $this->format_where($expression);
        }
        //3. group by statement
        if ($expression->has_groups()) {
            $sql .= $this->format_group_by($expression);
        }
        //4. order by statement
        if ($expression->has_orders()) {
            $sql .= $this->format_order_by($expression);
        }
        return $sql;
    }

    protected function format_update($expression) {

    }

    protected function format_insert($expression) {

    }

    protected function format_order_by($expression) {
        Args::check($expression instanceof QueryExpression, 'Invalid argument. Expected query expression');
        if (!array_key_exists('orderby', $expression->params)) {
            return '';
        }
        /**
         * @var MemberListExpression
         */
        $orders = $expression->params['orderby'];
        Args::check($orders instanceof MemberListExpression, 'Invalid order expression. Expected member list');
        if ($orders->count() == 0) {
            return '';
        }
        /**
         * @param SelectableExpression $expr
         * @return mixed
         */
        $map = function ($expr) {
            return $this->format($expr).($expr->order=='desc' ? ' DESC' : ' ASC');
        };
        return ' ORDER BY '.implode(', ', array_map($map, $orders->getArrayCopy()));
    }

    protected function format_group_by($expression) {
        Args::check($expression instanceof QueryExpression, 'Invalid argument. Expected query expression');
        if (!array_key_exists('groupby', $expression->params)) {
            return '';
        }
        /**
         * @type MemberListExpression
         */
        $groups = $expression->params['groupby'];
        Args::check($groups instanceof MemberListExpression, 'Invalid group by expression. Expected member list');
        if ($groups->count() == 0) {
            return '';
        }
        /**
         * @param SelectableExpression $expr
         * @return mixed
         */
        $map = function ($expr) {
            return $this->format($expr);
        };
        return ' GROUP BY '.implode(', ', array_map($map, $groups->getArrayCopy()));
    }

    protected function format_delete($expression) {

    }

    /**
     * @param QueryExpression $expression
     * @return string
     */
    protected function format_where($expression) {
        Args::check($expression instanceof QueryExpression, 'Invalid argument. Expected query expression');
        $expr = $expression->get_filter();
        if (is_null($expr)) {
            return '';
        }
        if ($expr instanceof ComparisonExpression) {
            return ' WHERE '.$this->format_comparison($expr);
        }
        else if ($expr instanceof LogicalExpression) {
            return ' WHERE '.$this->format_logical($expr);
        }
        throw new \InvalidArgumentException("Invalid filter expression. Expected a logical or comparison expression");
    }

    protected function format_logical($expr) {

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
        throw new InvalidArgumentException('Unsupported logical operator');
    }

    /**
     * @param MethodCallExpression $expr
     * @return mixed
     */
    protected function format_method($expr) {
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

    public function resolve_entity($closure) {
        Args::check($closure instanceof Closure, 'Invalid argument. Expected closure');
        $this->entity_resolver = $closure;
    }

    public function resolve_member($closure) {
        Args::check($closure instanceof Closure, 'Invalid argument. Expected closure');
        $this->entity_resolver = $closure;
    }

    protected function format_member($expr) {

        if ($this->member_resolver instanceof Closure) {
            $member = $this->member_resolver->call($this, $expr->name, $expr->entity);
            Args::string($member, 'Member');
            Args::not_blank($member, 'Member');
            if (is_null($expr->entity)) {
                return $this->escape_name($member);
            }
            else {
                return $this->format_entity($expr->entity).'.'.$this->escape_name($member);
            }
        }
        else {
            if (is_null($expr->entity))
                return $this->escape_name($expr->name);
            else
                return $this->format_entity($expr->entity).'.'.$this->escape_name($expr->name);
        }
    }

    protected function format_entity($expr) {
        Args::check(is_string($expr) || ($expr instanceof EntityExpression), 'Invalid argument. Expected string or an entity expression');
        if (is_string($expr)) {
            $entity = $expr;
        }
        else {
            $entity = $expr->name;
        }
        if ($this->entity_resolver instanceof Closure) {
            $entity = $this->entity_resolver->call($this, $entity);
            Args::string($entity, 'Entity');
            Args::not_blank($entity, 'Entity');
        }
        return $this->escape_name($entity);
    }

    /**
     * @param mixed $expr
     * @param string $format
     * @return mixed
     */
    public function format($expr, $format = NULL) {
        if ($expr instanceof EntityExpression) {
            return $this->format_entity($expr);
        }
        else if ($expr instanceof MemberExpression) {
            return $this->format_member($expr);
        }
        else if ($expr instanceof ComparisonExpression) {
            return $this->format_comparison($expr);
        }
        else if ($expr instanceof LogicalExpression) {
            return $this->format_logical($expr);
        }
        else if ($expr instanceof QueryExpression) {
            return $this->format_select($expr);
        }
        else if ($expr instanceof MethodCallExpression) {
            return $this->format_method($expr);
        }
        else if ($expr instanceof LiteralExpression) {
            return $this->escape($expr->value);
        }
        return NULL;
    }

}