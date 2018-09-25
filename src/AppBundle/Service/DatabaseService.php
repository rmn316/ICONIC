<?php

namespace AppBundle\Service;

use \MongoClient;
use \MongoDB;

class DatabaseService
{
    /**
     * @var MongoDB\Database
     */
    protected $database;

    /**
     * DatabaseService constructor.
     * @param MongoDB\Client $client
     * @param string $database
     */
    public function __construct(MongoDB\Client $client, $database)
    {
        $this->database = $client->selectDatabase($database);
    }

    /**
     * @return MongoDB\Database
     */
    public function getDatabase() : MongoDB\Database
    {
        return $this->database;
    }
}
