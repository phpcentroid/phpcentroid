<?php /** @noinspection PhpUnusedAliasInspection */

namespace PHPCentroid\Query;

use Closure;
use Error;
use PHPCentroid\Common\EventEmitter;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
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
    private EventEmitter $resolvingMethod;
    private Expr\Closure $current;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
        $this->resolvingMember = new EventEmitter();
        $this->resolvingJoinMember = new EventEmitter();
        $this->resolvingMethod = new EventEmitter();
    }

    /**
     * Parse a closure
     * @param mixed $closure
     * @return array
     * @throws ReflectionException
     */
    public function parse(Closure $closure): array
    {
        // get closure code
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
                // set current closure
                $this->current = $stmt;
                $expr = current($stmt->getStmts());
                if ($expr instanceof Stmt\Return_) {
                    if ($expr->expr instanceof Array_) {
                        return $this->parseSelect($stmt);
                    }
                }
            }
        }
        throw new Error('Invalid closure');
    }

    /**
     * @param Expr\Closure $closure
     * @return SelectableExpression[]
     */
    public function parseSelect(Expr\Closure $closure): array
    {
        $arr = array();
        $stmts = $closure->getStmts();
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

    public function parseMember(PropertyFetch $member): MemberExpression
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
                'member' => array_slice($qualified, -2),
                'qualifiedMember' => implode('.', $qualified)
            );
            $this->resolvingJoinMember->emit($event);
            if ($event->member instanceof SelectableExpression) {
                return $event->member;
            }
            return new MemberExpression($event->member);
        }
    }

    /**
     * @param Expr $expr
     * @return DataQueryExpression
     */
    public function parseCommon(Expr $expr): DataQueryExpression {
        if ($expr instanceof PropertyFetch) {
            return $this->parseMember($expr);
        }
        throw new Error("An expression of type " . get_class($expr) . " is not supported");
    }

}