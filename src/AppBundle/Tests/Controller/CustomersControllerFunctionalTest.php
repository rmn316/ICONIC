<?php

namespace AppBundle\Tests\Controller;

use Predis\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomersControllerFunctionalTest
 * @package AppBundle\Tests\Controller
 */
class CustomersControllerFunctionalTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \MongoDB\Client
     */
    private $database;

    /**
     * @var array
     */
    static $testData = ['name' => 'Test User', 'age' => 100];

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        /** @var $c ClientInterface */
        $c = $this->client->getContainer()->get('cache_connection');
        $c->flushall();

        $databaseClient = $this->client->getContainer()->get('database_connection');
        $this->database = $databaseClient->selectDatabase($this->client->getContainer()->getParameter('database_name'));
        $this->database->customers->drop();
    }

    private function loadDataFixtures() : void
    {
        $this->database->customers->insertOne(self::$testData);
    }

    /**
     * @param array $data
     * @param int $response
     *
     * @dataProvider createCustomerDataProvider
     */
    public function testCreateCustomers(array $data, $response)
    {
        $customers = json_encode($data);

        $this->client->request(Request::METHOD_POST, '/customers/', [], [], ['CONTENT_TYPE' => 'application/json'], $customers);
        $this->assertSame($response, $this->client->getResponse()->getStatusCode());
        $this->assertCacheHasBeenIs(false);
    }

    /**
     * @return array
     */
    public function createCustomerDataProvider() : array
    {
        return [
            [
                [['name' => 'Leandro', 'age' => 26], ['name' => 'Marcelo', 'age' => 30], ['name' => 'Alex', 'age' => 18]],
                Response::HTTP_OK
            ],
            [
                [],
                Response::HTTP_BAD_REQUEST
            ]
        ];
    }

    public function testGetCustomers() : void
    {
        // first run ... uses db. empty data.

        $this->client->request(Request::METHOD_GET, '/customers/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertSame([], json_decode($this->client->getResponse()->getContent()));

        // second run ... uses db.

        $this->loadDataFixtures();

        $this->client->request(Request::METHOD_GET, '/customers/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertNotEmpty(json_decode($this->client->getResponse()->getContent()));

        $this->assertCacheHasBeenIs(true);

        // uses cache ?

        $this->client->request(Request::METHOD_GET, '/customers/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertNotEmpty(json_decode($this->client->getResponse()->getContent()));

    }

    public function testDeleteCustomers() : void
    {
        $this->client->request(Request::METHOD_DELETE, '/customers/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCacheHasBeenIs(false);
    }

    public function assertCacheHasBeenIs($valid) : void
    {
        /** @var ClientInterface $cache */
        $cache = $this->client->getContainer()->get('cache_connection');
        $this->assertSame($valid, $cache->exists('customers'));
    }
}
