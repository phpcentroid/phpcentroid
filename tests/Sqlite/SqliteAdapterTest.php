<?php

namespace PHPCentroid\Tests\Sqlite;

use PHPCentroid\Query\QueryExpression;
use PHPCentroid\Sqlite\SqliteAdapter;
use PHPCentroid\Tests\App\TestApplication;
use PHPUnit\Framework\TestCase;

class SqliteAdapterTest extends TestCase
{

    public function testOpen()
    {
        $app = new TestApplication();
        $database = $app->realpath('db' . DIRECTORY_SEPARATOR . 'local.db');
        $db = new SqliteAdapter(array('database' => $database));
        $db->open();
        $this->assertNotNull($db->getRawConnection());
        $db->close();
    }

    /**
     * @throws \Exception
     */
    public function testExecuteSelect()
    {
        $app = new TestApplication();
        $database = $app->realpath('db' . DIRECTORY_SEPARATOR . 'local.db');
        $db = new SqliteAdapter(array('database' => $database));
        $db->open();
        $query = (new QueryExpression())->select(
            'id', 'name', 'dateCreated', 'dateModified'
        )->from('UserData')->where('name')->equal('alexis.rees@example.com');
        $result = $db->execute($query);
        $this->assertNotNull($result);
        $user = (object)$result[0];
        $this->assertEquals('alexis.rees@example.com', $user->name);
        $db->close();
    }


}
