<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/** @noinspection PhpUnusedAliasInspection */

namespace PHPCentroid\Query;

use Closure;
use Error;
use PHPCentroid\Common\EventEmitter;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Opis\Closure\SerializableClosure;
use Opis\Closure\ReflectionClosure;
use PHPUnit\Framework\Exception;
use ReflectionException;

use PHPCentroid\Common\Args;
use function _\size;
use function _\slice;

class ClosureParser {

    private Parser $parser;
    private EventEmitter $resolvingMember;
    private EventEmitter $resolvingJoinMember;
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    private EventEmitter $resolvingMethod;
    private array $params;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
        $this->resolvingMember = new EventEmitter();
        $this->resolvingJoinMember = new EventEmitter();
        $this->resolvingMethod = new EventEmitter();
    }

    /**
     * @throws ReflectionException
     */
    protected function getClosure(Closure $closure): Expr\Closure {
        $reflector = new ReflectionClosure($closure);
        $code = '<?php $body = ' . $reflector->getCode() . ';';
        // and parse
        $ast = $this->parser->parse($code);
        $expr = $ast[0];
        if ($expr instanceof Expression) {
            $stmt = $expr->expr;
            if ($stmt instanceof Assign) {
                $stmt = $stmt->expr;
            }
            if ($stmt instanceof Expr\Closure) {
                return $stmt;
            }
        }
        throw new ReflectionException('Invalid closure format');
    }

    protected function getParams(Expr\Closure $closure, array $values): array {
        $params = $closure->params;
        $arr = array();
        foreach ($params as $index => $param) {
            if ($index > 0) {
                $arr += [ $param->var->name => $values[$index - 1] ];
            }
        }
        return $arr;
    }

    /**
     * @throws ReflectionException
     */
    public function parseFilter(Closure $closure,mixed ...$params): array {
        $closureExpr = $this->getClosure($closure);
        $this->params = $this->getParams($closureExpr, $params);
        $stmts = $closureExpr->getStmts();
        $stmt = current($stmts);
        if ($stmt instanceof Stmt\Return_) {
            $expr = $stmt->expr;
            if ($expr instanceof BinaryOp) {
                return $this->parseCommon($expr);
            }
        }
        throw new Exception('Invalid filter closure. Expected a closure with a binary expression.');
    }

    /**
     * @param Closure $closure
     * @param mixed ...$params
     * @return array
     * @throws ReflectionException
     */
    public function parseSelect(Closure $closure,mixed ...$params): array
    {
        $closureExpr = $this->getClosure($closure);
        $arr = array();
        $stmts = $closureExpr->getStmts();
        $stmt = current($stmts);
        if ($stmt instanceof Stmt\Return_) {
            $expr = $stmt->expr;
            if ($expr instanceof Array_) {
                foreach ($expr->items as $item) {
                    if ($item->key instanceof String_) {
                        $arr[$item->key->value] = $this->parseCommon($item->value);
                    } else {
                        $arr[] = $this->parseCommon($item->value);
                    }
                }
            } else if ($expr instanceof PropertyFetch) {
                $arr[$expr->name->name] = $this->parseCommon($expr);
            }
        }
        return $arr;
    }

    public function parseMember(PropertyFetch $member): array
    {
        if ($member->var instanceof PropertyFetch) {
            $qualified = array($member->name->name);
            $var = $member->var;
            while($var instanceof PropertyFetch) {
                array_unshift($qualified, $var->name->name);
                if ($var->var instanceof PropertyFetch) {
                    $var = $var->var;
                } else {
                    $var = null;
                }
            }
            if (size($qualified) == 1) {
                $event = (object)array(
                    'target' => $this,
                    'member' => $qualified[0] // get first element of array
                );
                $this->resolvingMember->emit($member);
                if (is_array($event->member)) {
                    return $event->member;
                }
                return array('$getField' => $event->member);
            }
            $event = (object)array(
                'target' => $this,
                'member' => implode('.', array_slice($qualified, -2)),
                'fullyQualifiedMember' => implode('.', $qualified)
            );
            $this->resolvingJoinMember->emit($event);
            // member should be an instance of selectable expression
            if (is_array($event->member)) {
                return $event->member;
            }
            // or a string
            Args::check(is_string($event->member), 'Invalid member expression. Expected an instance of selectable expression or a string.');
            // split member
            $member = explode('.', $event->member);
            // for creating a member expression
            if (size($member) == 1) {
                return array('$getField' => $member[0]);
            }
            // with alias
            return array('$getField' => implode(',', $member));
        } else if ($member->var instanceof Variable) {
            return array('$getField' => $member->name->name);
        }
        throw new Exception('Invalid member expression');
    }

    /**
     * @throws Exception
     */
    public function parseLiteral(Scalar $expr): mixed {
        if (property_exists($expr, 'value')) {
            return $expr->value;
        }
        throw new Exception('Unsupported scalar expression');
    }

    /**
     * @throws Exception
     */
    public function parseMethodCall(Expr\FuncCall $expr): array {
        $args = array_map(function(Arg $arg) {
            return $this->parseCommon($arg->value);
        }, $expr->args);
        $name = $expr->name->name;
        $event = (object)array(
            'target' => $this,
            'method' => $name
        );
        $this->resolvingMethod->emit($event);
        if (is_array($event->method)) {
            return $event->method;
        }
        $escaped = '$' . $name;
        return array($escaped => $args);
    }

    public function parseBinary(BinaryOp $expr): array {
        $binaryOperator = $expr->getOperatorSigil();
        $left = $this->parseCommon($expr->left);
        $right = $this->parseCommon($expr->right);
        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
        switch ($binaryOperator) {
            case '==':
            case '===':
                return array('$eq' => array($left, $right));
            case '!=':
                return array('$ne' => array($left, $right));
            case '>':
                return array('$gt' => array($left, $right));
            case '<':
                return array('$lt' => array($left, $right));
            case '>=':
                return array('$ge' => array($left, $right));
            case '<=':
                return array('$le' => array($left, $right));
            case '&&':
                return array('$and' => array($left, $right));
            case '||':
                return array('$or' => array($left, $right));
            case '+':
                return array('$add' => array($left, $right));
            case '-':
                return array('$subtract' => array($left, $right));
            case '*':
                return array('$multiply' => array($left, $right));
            case '/':
                return array('$divide' => array($left, $right));
            case '%':
                return array('$bit' => array($left, $right));
            default:
                throw new Error("Unsupported operator " . $binaryOperator);
        }
    }

    public function parseVariable(Variable $expr): mixed
    {
        return $this->params[$expr->name];
    }

    /**
     * @param Expr $expr
     * @return DataQueryExpression
     */
    public function parseCommon(Expr $expr): mixed {
        if ($expr instanceof PropertyFetch) {
            return $this->parseMember($expr);
        } else if ($expr instanceof BinaryOp) {
            return $this->parseBinary($expr);
        } else if ($expr instanceof Scalar) {
            return $this->parseLiteral($expr);
        } else if ($expr instanceof Expr\FuncCall) {
            return $this->parseMethodCall($expr);
        } else if ($expr instanceof Variable) {
            return $this->parseVariable($expr);
        }
        throw new Error("An expression of type " . get_class($expr) . " is not supported");
    }

}