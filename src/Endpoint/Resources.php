<?php

namespace Opensaucesystems\Lxd\Endpoint;

class Resources extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/resources/';
    }

    /**
     * Show host information
     *
     * @return array
     */
    public function info()
    {
        return $this->get($this->getEndpoint());
    }
}
