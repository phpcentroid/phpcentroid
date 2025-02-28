<?php

namespace PHPCentroid\Tests\Query;

use PHPCentroid\Query\ClosureParser;
use PHPUnit\Framework\TestCase;

class ClosureParserTest extends TestCase
{
    public function test_parse()
    {
        $closure = function ($a, $b) {
            return $a + $b;
        };
        $parser = new ClosureParser();
        $ast = $parser->parse($closure);
        $this->assertIsArray($ast, 'ClosureParser::parse() should return an array');
    }
}