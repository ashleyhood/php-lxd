<?php

namespace Opensaucesystems\Lxd\Client\Containers;

class Snapshots
{
    public function __construct($client)
    {
        $this->client   = $client;
        $this->endpoint = $this->client->endpoint.'/';
    }

    /**
     * Get information on a snapshot
     *
     * Get information on a snapshot or when the name parameter is an
     * empty string, return an array of all the snapshots
     *
     * @param  string $name      Name of container
     * @param  string $snapshots Name of snapshots
     * @return mixed
     */
    public function get($name, $snapshot = '')
    {
        $endpoint = $this->endpoint.$name.'/snapshots/'.$snapshot;
        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
    }

    /**
     * List of snapshots for a container
     *
     * @param  string $name Name of container
     * @return array
     */
    public function all($name)
    {
        $endpoint  = $this->endpoint.$name.'/snapshots/';
        $snapshots = [];
        $response  = $this->get($name);

        foreach ($response as $snapshot) {
            $snapshots[] = str_replace($endpoint, '', strstr($snapshot, $endpoint));
        }

        return $snapshots;
    }

    /**
     * Get information on a snapshot
     *
     * @param string $name      Name of container
     * @param string $snapshots Name of snapshots
     * @return object
     */
    public function info($name, $snapshot)
    {
        if (empty($snapshot)) {
            throw new \Exception('Missing snapshot name');
        }

        return $this->get($name, $snapshot);
    }

    /**
     * Create a snapshot of a container
     *
     * If stateful is true when creating a snapshot of a
     * running container, the container's runtime state will be stored in the
     * snapshot.  Note that CRIU must be installed on the server to create a
     * stateful snapshot, or LXD will return a 500 error.
     *
     * @param  string $name     Name of container
     * @param  string $snapshot Name of snapshot
     * @param  bool   $stateful Whether to save runtime state for a running container
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function create($name, $snapshot, $stateful = false, $wait = false)
    {
        $opts['name']     = $snapshot;
        $opts['stateful'] = $stateful;

        $endpoint = $this->endpoint.$name.'/snapshots';
        $response = $this->client->connection->post($endpoint, $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }

    /**
     * Rename a snapshot
     *
     * @param string $name        Name of container
     * @param string $snaphot     Name of snapshot
     * @param string $newSnapshot Name of new snapshot
     * @param bool   $wait        Wait for operation to finish
     * @return object
     */
    public function rename($name, $snaphot, $newSnapshot, $wait = false)
    {
        $opts['name'] = $newSnapshot;
        $endpoint = $this->endpoint.$name.'/snapshots/'.$snaphot;
        $response = $this->client->connection->post($endpoint, $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }

    /**
     * Delete a container
     *
     * @param string $name    Name of container
     * @param string $snaphot Name of snapshot
     * @param bool   $wait    Wait for operation to finish
     * @return object
     */
    public function delete($name, $snaphot, $wait = false)
    {
        $endpoint = $this->endpoint.$name.'/snapshots/'.$snaphot;
        $response = $this->client->connection->delete($endpoint);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }
}
