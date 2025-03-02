<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/** @noinspection PhpUnusedAliasInspection */

namespace PHPCentroid\Query;

use Closure;
use Error;
use PHPCentroid\Common\EventEmitter;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
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

    /**
     * @throws ReflectionException
     */
    public function parseFilter(Closure $closure): array {
        $closureExpr = $this->getClosure($closure);
        $arr = array();
        $stmts = $closureExpr->getStmts();
        $stmt = current($stmts);
        if ($stmt instanceof Stmt\Return_) {
            $expr = $stmt->expr;
            if ($expr instanceof BinaryOp) {
                return $this->parseCommon($expr);
            }
        }
        return $arr;
    }

    /**
     * @param Closure $closure
     * @return SelectableExpression[]
     * @throws ReflectionException
     */
    public function parseSelect(Closure $closure): array
    {
        $closureExpr = $this->getClosure($closure);
        $arr = array();
        $stmts = $closureExpr->getStmts();
        $stmt = current($stmts);
        if ($stmt instanceof Stmt\Return_) {
            $expr = $stmt->expr;
            if ($expr instanceof Array_) {
                foreach ($expr->items as $item) {
                    // expect \PhpParser\Node\Expr\PropertyFetch
                    Args::check($item->value instanceof PropertyFetch, 'Expected a valid member expression');
                    $member = $this->parseCommon($item->value);
                    if ($member instanceof SelectableExpression) {
                        // check if key is a string and add as alias
                        if ($item->key instanceof String_) {
                            $member->as($item->key->value);
                        }
                        $arr[] = $member;
                    } else {
                        throw new Error('Expected a valid selectable expression');
                    }
                }
            }
        }
        return $arr;
    }

    public function parseMember(PropertyFetch $member): SelectableExpression
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
                if ($event->member instanceof SelectableExpression) {
                    return $event->member;
                }
                return new MemberExpression($event->member);
            }
            $event = (object)array(
                'target' => $this,
                'member' => implode('.', array_slice($qualified, -2)),
                'fullyQualifiedMember' => implode('.', $qualified)
            );
            $this->resolvingJoinMember->emit($event);
            // member should be an instance of selectable expression
            if ($event->member instanceof SelectableExpression) {
                return $event->member;
            }
            // or a string
            Args::check(is_string($event->member), 'Invalid member expression. Expected an instance of selectable expression or a string.');
            // split member
            $member = explode('.', $event->member);
            // for creating a member expression
            if (size($member) == 1) {
                return new MemberExpression($member[0]);
            }
            // with alias
            return (new MemberExpression($member[1]))->from($member[0]);
        } else if ($member->var instanceof Variable) {
            return new MemberExpression($member->name->name);
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
        }
        throw new Error("An expression of type " . get_class($expr) . " is not supported");
    }

}