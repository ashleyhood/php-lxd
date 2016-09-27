<?php

namespace Opensaucesystems\Lxd\Endpoint;

use Opensaucesystems\Lxd\Exception\OperationException;

class Operations extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/operations/';
    }

    /**
     * List all background operations on the server
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $operations = [];

        foreach ($this->get($this->getEndpoint()) as $key => $operation) {
            $operations[$key] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $operation);
        }

        return $operations;
    }

    /**
     * Get information on a certificate
     *
     * @param  string $uuid UUID of background operation
     * @return array
     */
    public function info($uuid)
    {
        return $this->get($this->getEndpoint().$uuid);
    }

    /**
     * Cancel an operation
     *
     * Calling this will change the state to "cancelling"
     * rather than actually removing the entry
     *
     * @param  string $uuid UUID of background operation
     */
    public function cancel($uuid)
    {
        return $this->delete($this->getEndpoint().$uuid);
    }

    /**
     * Wait for an operation to finish
     *
     * @param  string $uuid    UUID of background operation
     * @param  int    $timeout Max time to wait
     * @return array
     */
    public function wait($uuid, $timeout = null)
    {

        $endpoint = $this->getEndpoint().$uuid.'/wait';

        if (is_numeric($timeout) && $timeout > 0) {
            $endpoint .= '?timeout='.$timeout;
        }

        return $this->get($endpoint);
    }
}
