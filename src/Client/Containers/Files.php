<?php

namespace Opensaucesystems\Lxd\Client\Containers;

class Files
{
    public function __construct($client)
    {
        $this->client   = $client;
        $this->endpoint = $this->client->endpoint.'/';
    }

    /**
     * Read the contents of a file in a container
     *
     * @param  string $name     Name of container
     * @param  string $filepath Full path to a file within the container
     * @return object
     */
    public function read($name, $filepath)
    {
        $endpoint = $this->endpoint.$name.'/files?path='.$filepath;
        $response = $this->client->connection->get($endpoint);

        return $response;
    }

    /**
     * Write to a file in a container
     *
     *
     * @param  string $name     Name of container
     * @param  string $filepath Path to the output file in the container
     * @param  string $data     Data to write to the file
     * @return object
     */
    public function write($name, $filepath, $data, $uid = null, $gid = null, $mode = null)
    {
        $headers = [];

        if (is_numeric($uid)) {
            $headers['X-LXD-uid'] = $uid;
        }

        if (is_numeric($gid)) {
            $headers['X-LXD-gid'] = $gid;
        }

        if (is_numeric($mode)) {
            $headers['X-LXD-mode'] = $mode;
        }

        $endpoint = $this->endpoint.$name.'/files?path='.$filepath;
        $response = $this->client->connection->post($endpoint, $data, $headers);

        return $response->body->metadata;
    }
}
