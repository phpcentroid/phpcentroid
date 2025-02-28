<?php

namespace PhpCentroid\Query;
use PhpParser\ParserFactory;
use function Opis\Closure\{serialize, unserialize};

class ClosureParser {

    private  $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
    }

    /**
     * Parse a closure 
     * @param mixed $closure
     * @return \PhpParser\Node\Stmt[]|null
     */
    public function parse($closure) {
        // get closure code
        // $reflector = new ReflectionClosure($closure);
        $code = '<?php $body = ' . serialize($closure) . ';';
        // and parse
        $ast = $this->parser->parse($code);
        return $ast;
    }

}