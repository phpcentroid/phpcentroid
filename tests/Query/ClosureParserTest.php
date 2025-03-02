<?php

namespace PHPCentroid\Tests\Query;

use Exception;
use PHPCentroid\Query\ClosureParser;
use PHPCentroid\Query\MemberExpression;
use PHPCentroid\Query\SqlFormatter;
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
        $ast = $parser->parseSelect($closure);
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
        $select = $parser->parseSelect($closure);
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
        $select = $parser->parseSelect($closure);
        $this->assertIsArray($select, 'ClosureParser::parseSelect() should return an array');
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testParseFilterExpr()
    {
        $closure = function ($a) {
            return $a->name === 'admin';
        };
        $parser = new ClosureParser();
        $filter = $parser->parseFilter($closure);
        $this->assertIsArray($filter, 'ClosureParser::parseFilter() should return an array');
        $formatter = new SqlFormatter();
        $sql = $formatter->escape($filter);
        $this->assertIsString($sql, 'SqlFormatter::format() should return a string');
        $this->assertEquals("`name` = 'admin'", $sql);

    }

    /**
     * @throws Exception
     */
    public function testParseAndExpression()
    {
        $closure = function ($a) {
            return $a->category === 'Laptops' && $a->price < 1000;
        };
        $parser = new ClosureParser();
        $filter = $parser->parseFilter($closure);
        $this->assertIsArray($filter, 'ClosureParser::parseFilter() should return an array');
        $formatter = new SqlFormatter();
        $sql = $formatter->escape($filter);
        $this->assertIsString($sql, 'SqlFormatter::format() should return a string');
        $this->assertEquals("(`category` = 'Laptops' AND `price` < 1000)", $sql);

    }
}