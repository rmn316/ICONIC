<?php

namespace AppBundle\Tests\Service;

use AppBundle\Service\CacheService;
use Predis\ClientInterface;
use Predis\Connection\ConnectionInterface;

class CacheServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CacheService
     */
    private $service;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Predis\ClientInterface
     */
    private $cache;

    public function setUp()
    {
        $this->cache = $this->getMockBuilder(ClientInterface::class)->getMock();

        $this->service = new CacheService($this->cache);
    }


    public function testGetWithAInvalidConnectionReturnsEmptyString()
    {
        $connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();

        $this->cache->expects($this->once())
            ->method('getConnection')
            ->with()
            ->willReturn($connection);

        $connection->expects($this->once())
            ->method('isConnected')
            ->with()
            ->willReturn(false);

        $result = $this->service->get('some_key');
        $this->assertSame('', $result);
    }

    /**
     * @param string $expectedResponse
     * @param bool $isExisting
     *
     * @dataProvider cacheServiceGetWithSuccessDataProvider
     */
    public function testGetWithAValidConnection($expectedResponse, $isExisting)
    {
        $connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();

        $this->cache->expects($this->once())
            ->method('getConnection')
            ->with()
            ->willReturn($connection);

        $connection->expects($this->once())
            ->method('isConnected')
            ->with()
            ->willReturn(true);

        $this->cache->expects($this->at(1))
            ->method('__call')
            ->with(
                $this->equalTo('exists'),
                $this->equalTo(['some_key'])
            )
            ->willReturn($isExisting);

        if ($isExisting) {
            $this->cache->expects($this->at(2))
                ->method('__call')
                ->with($this->equalTo('get'), $this->equalTo(['some_key']))
                ->willReturn($expectedResponse);
        }

        $result = $this->service->get('some_key');
        $this->assertSame($expectedResponse, $result);
    }

    /**
     * @return array
     */
    public function cacheServiceGetWithSuccessDataProvider() : array
    {
        return [
            'success_with_data' => ['some_result', true],
            'success_with_no_data' => ['', true]
        ];
    }

    public function testSetWithAInvalidConnection() : void
    {
        $this->assertConnection(false);

        $this->cache->expects($this->once())
            ->method('connect')
            ->with();

        $this->cache->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('set'), $this->equalTo(['some_key', 'some_value']));

        $this->service->set('some_key', 'some_value');
    }

    public function testSetWithAValidConnection() : void
    {
        $this->assertConnection(true);

        $this->cache->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('set'), $this->equalTo(['some_key', 'some_value']));

        $this->service->set('some_key', 'some_value');
    }

    public function testDeleteWithAInvalidConnection() : void
    {
        $this->assertConnection(false);

        $this->cache->expects($this->once())
            ->method('connect')
            ->with();

        $this->cache->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo([['some_key']]));

        $this->service->del('some_key');
    }

    public function testDeleteWithAValidConnection() : void
    {
        $this->assertConnection(true);

        $this->cache->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo([['some_key']]));

        $this->service->del('some_key');
    }

    /**
     * @param bool $alive
     */
    private function assertConnection($alive) : void
    {
        $connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();

        $this->cache->expects($this->once())
            ->method('getConnection')
            ->with()
            ->willReturn($connection);

        $connection->expects($this->once())
            ->method('isConnected')
            ->with()
            ->willReturn($alive);
    }

}