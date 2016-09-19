<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;
use Opensaucesystems\Lxd\Exception\OperationException;

class Operations
{
    /**
     * A LXD Operation
     */
    public function __construct($client)
    {
        $this->client   = $client;
        $this->endpoint = $this->client->endpoint.'/';

        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }
    }

    /**
     * Get information on background operation
     *
     * Get information on background operation or when the uuid
     * parameter is an empty string, return an array
     * of all the background operations
     *
     * @param  string $uuid UUID of background operation
     * @return mixed
     */
    public function get($uuid = '')
    {
        $endpoint = $this->endpoint.$uuid;
        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
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
        $response = $this->get();
        foreach ($response as $operation) {
            $operations[] = str_replace($this->endpoint, '', strstr($operation, $this->endpoint));
        }

        return $operations;
    }

    /**
     * Get information on a certificate
     *
     * @param  string $uuid UUID of background operation
     * @return object
     */
    public function info($uuid)
    {
        if (empty($uuid)) {
            throw new \Exception('Missing operation UUID');
        }

        return $this->get($uuid);
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
        if (empty($uuid)) {
            throw new \Exception('Missing operation UUID');
        }

        $endpoint = $this->endpoint.$uuid;
        $response = $this->client->connection->delete($endpoint);

        if ($response->body->status_code !== 202) {
            throw new \Exception('Operation not cancelled: '.$response->body->error);
        }
    }

    /**
     * Wait for an operation to finish
     *
     * @param  string $uuid UUID of background operation
     * @return object
     */
    public function wait($uuid, $timeout = null)
    {
        if (empty($uuid)) {
            throw new \Exception('Missing operation UUID');
        }

        $endpoint = $this->endpoint.$uuid.'/wait';

        if (is_numeric($timeout) && $timeout > 0) {
            $endpoint .= '?timeout='.$timeout;
        }

        $response = $this->client->connection->get($endpoint);

        if ($response->body->metadata->status_code !== 200) {
            $msg = 'Operation '.$response->body->metadata->status.': '.$response->body->metadata->err;
            throw new OperationException($msg);
        }
    }
}
