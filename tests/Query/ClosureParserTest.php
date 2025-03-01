<?php

namespace PHPCentroid\Tests\Query;

use PHPCentroid\Query\ClosureParser;
use PHPCentroid\Query\MemberExpression;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ClosureParserTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testParseSelect()
    {
        $closure = function ($a) {
            return array($a->id, $a->name, $a->age);
        };
        $parser = new ClosureParser();
        $ast = $parser->parse($closure);
        $this->assertIsArray($ast, 'ClosureParser::parse() should return an array');
        $this->assertCount(3, $ast, 'ClosureParser::parse() should return an array with 3 elements');
        foreach ($ast as $item) {
            $this->assertTrue($item instanceof MemberExpression, 'ClosureParser::parse() should return a MemberExpression');
        }
    }

    /**
     * @throws ReflectionException
     */
    public function testParseSelectWithAlias()
    {
        $closure = function ($a) {
            return array(
                'id' => $a->id,
                'name' => $a->name,
                'age' => $a->age
            );
        };
        $parser = new ClosureParser();
        $select = $parser->parse($closure);
        $this->assertIsArray($select, 'ClosureParser::parse() should return an array');
        $this->assertCount(3, $select, 'ClosureParser::parse() should return an array with 3 elements');
        foreach ($select as $item) {
            $this->assertTrue($item instanceof MemberExpression, 'ClosureParser::parse() should return a MemberExpression');
        }
        $first = current($select);
        $this->assertEquals('id', $first->alias, 'MemberExpression::alias should be set to the key of the array');
    }

    /**
     * @throws ReflectionException
     */
    public function testParseSelectQualifiedMember()
    {
        $closure = function ($a) {
            return array(
                $a->actionStatus->alternateName,
            );
        };
        $parser = new ClosureParser();
        $select = $parser->parse($closure);
        $this->assertIsArray($select, 'ClosureParser::parse() should return an array');
    }
}