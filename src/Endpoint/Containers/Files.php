<?php

namespace Opensaucesystems\Lxd\Endpoint\Containers;

use Opensaucesystems\Lxd\Endpoint\AbstructEndpoint;

class Files extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/containers/files/';
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
        return $this->get($this->getEndpoint().$name.'/files?path='.$filepath);
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

        return $this->post($this->getEndpoint().$name.'/files?path='.$filepath, $data, $headers);
    }
}
