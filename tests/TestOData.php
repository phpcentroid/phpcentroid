<?php

use PHPCentroid\Query\MemberListExpression;
use PHPCentroid\Query\OpenDataParser;
use PHPUnit\Framework\TestCase;
use PHPCentroid\Query\MemberExpression;
use PHPCentroid\Query\SqlFormatter;

class TestOData extends TestCase
{
    public function test_FilterExpression() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("familyName eq 'Thomas' and giveName eq 'John'");
        $formatter = new SqlFormatter();
        $this->assertEquals("((`familyName` = 'Thomas') AND (`giveName` = 'John'))",$formatter->format($expr));
    }

    public function test_OrExpression() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("category eq 'Desktops' or category eq 'Laptops'");
        $formatter = new SqlFormatter();
        $this->assertEquals("((`category` = 'Desktops') OR (`category` = 'Laptops'))" ,$formatter->format($expr));
    }



    public function test_AttributeWithAlias() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("name as productName");
        $member_expr = $expr[0];
        $this->assertTrue($member_expr instanceof MemberExpression, 'Expected member');
    }

    public function test_OrAndExpression() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("(category eq 'Desktops' or category eq 'Laptops') and (price le 500)");
        $formatter = new SqlFormatter();
        $this->assertEquals("(((`category` = 'Desktops') OR (`category` = 'Laptops')) AND (`price` <= 500))" ,$formatter->format($expr));
    }

    public function test_MemberCollection() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("id, name as productName, dateCreated");
        $this->assertTrue($expr instanceof MemberListExpression, 'Expected member');
    }

    public function test_CollectionWithAggregation() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("count(id) as totalCount, category");
        $this->assertTrue($expr instanceof MemberListExpression, 'Expected member');
    }

    public function test_OrderExpression() {
        $parser = new OpenDataParser();
        $expr = $parser->parse("familyName desc, givenName asc");
        $this->assertTrue($expr instanceof MemberListExpression, 'Expected member');
    }

}
