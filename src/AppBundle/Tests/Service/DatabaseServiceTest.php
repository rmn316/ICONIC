<?php

namespace AppBundle\Tests\Service;

use AppBundle\Service\DatabaseService;
use MongoDB\Client;
use MongoDB\Database;

class DatabaseServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetDatabase()
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();

        $client->expects($this->once())
            ->method('selectDatabase')
            ->with('some_db')
            ->willReturn($this->getMockBuilder(Database::class)->disableOriginalConstructor()->getMock());

        $service = new DatabaseService($client, 'some_db');

        $this->assertInstanceOf(Database::class, $service->getDatabase());
    }
}
