<?php

namespace Opensaucesystems\Lxd\Endpoint\Containers;

use Opensaucesystems\Lxd\Endpoint\AbstructEndpoint;

class Logs extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/containers/';
    }

    /**
     * List of logs for a container
     *
     * @param  string $name Name of container
     * @return array
     */
    public function all($name)
    {
        $logs = [];

        foreach ($this->get($this->getEndpoint().$name.'/logs/') as $log) {
            $logs[] = str_replace(
                '/'.$this->client->getApiVersion().'/containers/'.$name.'/logs/',
                '',
                $log
            );
        }

        return $logs;
    }

    /**
     * Get the contents of a particular log file
     *
     * @param string $name  Name of container
     * @param string $log   Name of log
     * @return object
     */
    public function read($name, $log)
    {
        return $this->get($this->getEndpoint().$name.'/logs/'.$log);
    }

    /**
     * Remove a particular log file
     *
     * @param string $name  Name of container
     * @param string $log   Name of log
     * @return object
     */
    public function remove($name, $log)
    {
        return $this->delete($this->getEndpoint().$name.'/logs/'.$log);
    }
}
