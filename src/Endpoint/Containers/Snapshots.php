<?php

namespace Opensaucesystems\Lxd\Endpoint\Containers;

use Opensaucesystems\Lxd\Endpoint\AbstructEndpoint;

class Snapshots extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/containers/';
    }

    /**
     * List of snapshots for a container
     *
     * @param  string $name Name of container
     * @return array
     */
    public function all($name)
    {
        $snapshots = [];

        foreach ($this->get($this->getEndpoint().$name.'/snapshots/') as $snapshot) {
            $snapshots[] = str_replace('/'.$this->client->getApiVersion().'/containers/'.$name.'/snapshots/', '', $snapshot);
        }

        return $snapshots;
    }

    /**
     * Show information on a snapshot
     *
     * @param string $name      Name of container
     * @param string $snapshots Name of snapshots
     * @return object
     */
    public function info($name, $snapshot)
    {
        return $this->get($this->getEndpoint().$name.'/snapshots/'.$snapshot);
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

        $response = $this->post($this->getEndpoint().$name.'/snapshots', $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
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
        $response = $this->post($this->getEndpoint().$name.'/snapshots/'.$snaphot, $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Delete a container
     *
     * @param string $name    Name of container
     * @param string $snaphot Name of snapshot
     * @param bool   $wait    Wait for operation to finish
     * @return object
     */
    public function remove($name, $snaphot, $wait = false)
    {
        $response = $this->delete($this->getEndpoint().$name.'/snapshots/'.$snaphot);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }
}
