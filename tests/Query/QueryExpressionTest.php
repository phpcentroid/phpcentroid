<?php

namespace PHPCentroid\Tests\Query;

use PHPCentroid\Query\CountExpression;
use PHPCentroid\Query\MemberExpression;
use PHPCentroid\Query\MethodCallExpression;
use PHPUnit\Framework\TestCase;

class QueryExpressionTest extends TestCase
{
    public function test_MemberExpression()
    {
        $member = new MemberExpression('name');
        var_dump($member);
        $this->assertEquals('name', $member->name, 'MemberExpression has wrong name');
    }

    public function test_MethodExpressionArgCount()
    {
        $method = new MethodCallExpression('count');
        $method->args[] = new MemberExpression('id');
        $this->assertCount(1, $method->args, 'Method has wrong number of arguments');
    }

    public function test_CountExpression()
    {
        $method = new CountExpression(new MemberExpression('id'));
        $this->assertEquals('count', $method->method, 'Method has wrong name');
    }

    public function test_AddExpressionToString()
    {
        $expr = new \PHPCentroid\Query\ArithmeticExpression('price', 'add', 10);
        $this->assertMatchesRegularExpression('/^\(price add 10\)$/', (string)$expr, 'ArithmeticExpression returns wrong string');
    }

    public function test_SubtractExpressionToString()
    {
        $expr = new \PHPCentroid\Query\ArithmeticExpression('price', 'sub', 10);
        $this->assertMatchesRegularExpression('/^\(price sub 10\)$/', (string)$expr, 'ArithmeticExpression returns wrong string');
    }

    public function test_MultiplyExpressionToString()
    {
        $expr = new \PHPCentroid\Query\ArithmeticExpression('price', 'mul', 0.75);
        $this->assertMatchesRegularExpression('/^\(price mul 0.75\)$/', (string)$expr, 'ArithmeticExpression returns wrong string');
    }

    public function test_LogicalExpressionToString()
    {
        $expr = new \PHPCentroid\Query\LogicalExpression('or', array(
            new \PHPCentroid\Query\ComparisonExpression('category', 'eq', 'Laptop'),
            new \PHPCentroid\Query\ComparisonExpression('category', 'eq', 'Desktop')
        ));
        $this->assertMatchesRegularExpression('/^\(category eq \'Laptop\' or category eq \'Desktop\'\)$/', (string)$expr, 'LogicalExpression returns wrong string');
    }

    public function test_QueryExpressionSelectToSql()
    {
        $q = new \PHPCentroid\Query\QueryExpression();
        $q->select("id", "givenName", "familyName")
            ->also_select((new MemberExpression('dateCreated'))
                ->as('created'))
            ->from("Person");
        $formatter = new \PHPCentroid\Query\SqlFormatter();
        $this->assertEquals('SELECT `id`, `givenName`, `familyName`, `dateCreated` AS `created` FROM `Person`', $formatter->format($q), 'Wrong SQL statement');
    }

    public function test_QueryExpressionSelectAndOrder()
    {
        $q = new \PHPCentroid\Query\QueryExpression();
        $q->select('id', 'givenName', 'familyName')
            ->also_select(MemberExpression::create('dateCreated')
                ->as('created'))
            ->order_by('familyName', 'givenName')
            ->from('Person');
        $formatter = new \PHPCentroid\Query\SqlFormatter();
        var_dump($q);
        $this->assertEquals('SELECT `id`, `givenName`, `familyName`, `dateCreated` AS `created` FROM `Person` ORDER BY `familyName` ASC, `givenName` ASC', $formatter->format($q), 'Wrong SQL statement');
    }

    public function test_QueryExpressionSelectAndGroup()
    {
        $q = new \PHPCentroid\Query\QueryExpression();
        $q->select(new CountExpression(new MemberExpression('id')), 'category')
            ->group_by('category')
            ->from('Product');
        $formatter = new \PHPCentroid\Query\SqlFormatter();
        var_dump($q);
        $this->assertEquals('SELECT COUNT(`id`), `category` FROM `Product` GROUP BY `category`', $formatter->format($q), 'Wrong SQL statement');
    }

    public function test_QueryExpressionSelectAndWhere()
    {
        $q = new \PHPCentroid\Query\QueryExpression();
        $q->select('id', 'name', 'model', 'category')
            ->where('category')->equal('Laptop')
            ->either('category')->equal('Desktop')
            ->from('Product');
        $formatter = new \PHPCentroid\Query\SqlFormatter();
        var_dump($formatter->format($q));
        var_dump($q);
        $this->assertEquals("SELECT `id`, `name`, `model`, `category` FROM `Product` WHERE ((`category` = 'Laptop') OR (`category` = 'Desktop'))", $formatter->format($q), 'Wrong SQL statement');
    }

    public function test_QueryExpressionSelectAndPrepare()
    {
        $q = new \PHPCentroid\Query\QueryExpression();
        $q->select('id', 'name', 'model', 'category')
            ->where('category')->equal('Laptop')
            ->either('category')->equal('Desktop')
            ->prepare()
            ->where('price')->lower_than(500)
            ->from('Product');
        $formatter = new \PHPCentroid\Query\SqlFormatter();
        var_dump($q);
        $this->assertEquals("SELECT `id`, `name`, `model`, `category` FROM `Product` WHERE (((`category` = 'Laptop') OR (`category` = 'Desktop')) AND (`price` < 500))", $formatter->format($q), 'Wrong SQL statement');
    }

    public function test_DateStrings()
    {
        var_dump(date_format(new DateTime('2015-10-31T12:45:45+03:00'), DateTime::ISO8601));
        var_dump(date_format(new DateTime('2015-10-31T09:45:45Z'), DateTime::ISO8601));
    }

}
