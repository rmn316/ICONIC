<?php

namespace AppBundle\Service;

use Predis;

/**
* Here you have to implement a CacheService with the operations above.
* It should contain a failover, which means that if you cannot retrieve
* data you have to hit the Database.
**/
class CacheService
{
    /**
     * @var Predis\ClientInterface
     */
    private $client;

    /**
     * CacheService constructor.
     * @param Predis\ClientInterface $client
     */
    public function __construct(Predis\ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key) : string
    {
        if ($this->isValidConnection() && $this->client->exists($key)) {
            return $this->client->get($key);
        } else {
            return "";
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function set($key, $value) : void
    {
        if (!$this->isValidConnection()) {
            $this->client->connect();
        }

        $this->client->set($key, $value);
    }

    /**
     * @param string $key
     * @return void
     */
    public function del($key) : void
    {
        if (!$this->isValidConnection()) {
            $this->client->connect();
        }

        $this->client->del([$key]);
    }

    /**
     * @return bool
     */
    private function isValidConnection() : bool
    {
        if (!$this->client->getConnection()->isConnected()) {
            return false;
        } else {
            return true;
        }
    }
}
