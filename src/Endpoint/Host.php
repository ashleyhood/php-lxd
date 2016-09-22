<?php

namespace Opensaucesystems\Lxd\Endpoint;

use Opensaucesystems\Lxd\Client;
use Opensaucesystems\Lxd\HttpClient\Message\ResponseMediator;

class Host extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '';
    }

    /**
     * A LXD Host
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     *  Server configuration and environment information
     *
     * @return object
     */
    public function show()
    {
        return $this->get($this->getEndpoint());
    }

    /**
     * Does the server trust the client
     *
     * @return bool
     */
    public function trusted()
    {
        $info = $this->show();

        return $info['auth'] === 'trusted' ? true : false;
    }

    /**
     * Updates the server configuration or other properties
     *
     * Example: Change trust password
     *  $info = $lxd->show();
     *  $info['config']['core.trust_password'] = "my-new-password";
     *  $lxd->update($config);
     *
     * @param  object $config replaces any existing config with the provided one
     * @return object
     */
    // public function update($config)
    // {
    //     $data['config'] = $config;
    //     $response = $this->patch($this->getEndpoint(), $config);

    //     return $this->show();
    // }

    /**
     * Replaces the server configuration or other properties
     *
     * Example: Change image updates
     *  $info = $lxd->show();
     *  $info['config']['images.auto_update_interval'] = '24';
     *  $lxd->update($info['config']);
     *
     * @param  object $config replaces any existing config with the provided one
     * @return
     */
    public function replace($config)
    {
        $data['config'] = $config;
        $response = $this->put($this->getEndpoint(), $data);

        return $this->show();
    }
}
