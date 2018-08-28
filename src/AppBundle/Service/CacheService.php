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
    public function __construct($host, $port, $prefix)
    {

    }

    public function get($key)
    {

    }

    public function set($key, $value)
    {

    }

    public function del($key)
    {

    }
}
