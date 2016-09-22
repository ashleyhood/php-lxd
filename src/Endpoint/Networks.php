<?php

namespace Opensaucesystems\Lxd\Endpoint;

class Networks extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/networks/';
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
        $response = $this->get($this->getEndpoint());

        foreach ($response as $network) {
            $networks[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $network);
        }

        return $networks;
    }

    /**
     * Show information on a network
     *
     * @param  string $name name of network
     * @return object
     */
    public function show($name)
    {
        return $this->get($this->getEndpoint().$name);
    }
}
