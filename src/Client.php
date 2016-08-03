<?php

namespace Opensaucesystems\Lxd;

use Opensaucesystems\Lxd\Exception\EndpointException;
use Opensaucesystems\Lxd\Exception\ClientConnectionException;
use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;
use Opensaucesystems\Lxd\Exception\ServerException;
use Httpful\Exception\ConnectionErrorException;

class Client
{
    private $info;

    /**
     * Create a new lxd client Instance
     */
    public function __construct(Connection $con)
    {
        $this->connection = $con;
        $this->syncInfo();
    }

    /**
     *  Server configuration and environment information
     * 
     * @return object
     */
    public function info()
    {
        return $this->info;
    }

    public function syncInfo()
    {
        $response = $this->connection->get();

        $this->info = $response->body->metadata;

        return $response->body->metadata;
    }

    /**
     * Does the server trust the client
     * 
     * @return bool
     */
    public function trusted()
    {
        return $this->info->auth === 'trusted' ? true : false;
    }

    /**
     * Replaces the server configuration or other properties
     * 
     * Example: Change trust password
     *  $config = $lxd->info()->config;
     *  $config->{'core.trust_password'} = "my-new-password";
     *  $lxd->create($config);
     * 
     * @param  object $config replaces any existing config with the provided one
     * @return object
     */
    // public function create($config)
    // {
    //     $response = $this->connection->put('', $config);

    //     return $response->body->metadata;
    // }

    /**
     * Updates the server configuration or other properties
     * 
     * Example: Change image updates
     *  $config = $lxd->info()->config;
     *  $config->{'images.auto_update_interval'} = '24';
     *  $lxd->update($config);
     * 
     * @param  object $config replaces any existing config with the provided one
     * @return
     */
    public function update($config)
    {
        if (!$this->trusted()) {
            throw new ClientAuthenticationFailed();
        }

        $data['config'] = $config;

        $response = $this->connection->put('', $data);

        if ($response->body->status_code !== 200) {
            throw new ServerException('Config not updated: '.$response->body->error);
        }

        $this->syncInfo();

        return $this->info()->config;
    }

    public function __get($endpoint)
    {
        $class = __NAMESPACE__.'\\Client\\'.ucfirst($endpoint);

        if (class_exists($class)) {
            $this->endpoint = $endpoint;

            return new $class($this);
        } else {
            throw new EndpointException(
                'Endpoint '.$class.', not implemented.'
            );
        }
    }
}
