<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Client;
use Opensaucesystems\Lxd\HttpClient\Message\ResponseMediator;
use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;

class Host extends AbstructEndpoint
{
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
    public function info()
    {
        return $this->get('');
    }

    /**
     * Does the server trust the client
     *
     * @return bool
     */
    public function trusted()
    {
        $info = $this->info();

        return $info['auth'] === 'trusted' ? true : false;
    }

    /**
     * Updates the server configuration or other properties
     *
     * Example: Change trust password
     *  $info = $lxd->info();
     *  $info['config']['core.trust_password'] = "my-new-password";
     *  $lxd->update($config);
     *
     * @param  object $config replaces any existing config with the provided one
     * @return object
     */
    // public function update($config)
    // {
    //     if (!$this->trusted()) {
    //         throw new ClientAuthenticationFailed();
    //     }

    //     $data['config'] = $config;
    //     $response = $this->patch('', $config);

    //     return $this->info();
    // }

    /**
     * Replaces the server configuration or other properties
     *
     * Example: Change image updates
     *  $info = $lxd->info();
     *  $info['config']['images.auto_update_interval'] = '24';
     *  $lxd->update($info['config']);
     *
     * @param  object $config replaces any existing config with the provided one
     * @return
     */
    public function replace($config)
    {
        if (!$this->trusted()) {
            throw new ClientAuthenticationFailed();
        }

        $data['config'] = $config;
        $response = $this->put('', $data);

        return $this->info();
    }
}
