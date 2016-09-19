<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Client;
use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;

class Networks
{
    /**
     * A LXD Network
     */
    public function __construct(Client $client)
    {
        $this->client   = $client;
        $this->endpoint = $this->client->endpoint.'/';

        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }
    }

    /**
     * Get network information
     *
     * Get network information or when the name parameter
     * is an empty string, return an array of
     * all the network information
     *
     * @param  string $name name of network
     * @return mixed
     */
    public function get($name = '')
    {
        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
    }

    /**
     * List of networks
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $networks = [];
        $response = $this->get();

        foreach ($response as $network) {
            $networks[] = str_replace($this->endpoint, '', strstr($network, $this->endpoint));
        }

        return $networks;
    }

    /**
     * Get information on a network
     *
     * @param  string $name name of network
     * @return object
     */
    public function info($name)
    {
        return $this->get($name);
    }
}
