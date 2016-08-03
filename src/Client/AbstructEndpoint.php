<?php

namespace Opensaucesystems\Lxd\Client;

abstract class AbstructEndpoint
{
    protected $client;

    public function __construct($client, $class)
    {
        $this->client = $client;
        $this->class = $class;
    }

    public function __get($endpoint)
    {
        $class = $this->class.'\\'.ucfirst($endpoint);

        if (class_exists($class)) {
            return new $class($this->client);
        } else {
            throw new \Exception('Endpoint '.$class.', not implemented.');
        }
    }
}
