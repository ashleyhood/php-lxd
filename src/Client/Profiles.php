<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;

class Profiles
{
    /**
     * A LXD Profile
     */
    public function __construct($client)
    {
        $this->client = $client;
        $this->endpoint = $this->client->endpoint.'/';

        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }
    }

    /**
     * Get profile configuration
     *
     * Get profile configuration or when the name
     * parameter is an empty string, return
     *  an array of all the profiles
     *
     * @param  string $name name of profile
     * @return mixed
     */
    public function get($name = '')
    {
        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->get($endpoint);

        $this->name        = $name;
        $this->description = $response->body->metadata->description;
        $this->config      = $response->body->metadata->config;
        $this->devices     = $response->body->metadata->devices;

        return $this;
    }

    /**
     * List all profiles on the server
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $profiles = [];
        $response = $this->get();

        foreach ($response as $profile) {
            $profiles[] = str_replace($this->endpoint, '', strstr($profile, $this->endpoint));
        }

        return $profiles;
    }

    /**
     * Get information on a profile
     *
     * @param  string $name name of profile
     * @return object
     */
    public function info($name)
    {
        return $this->get($name);
    }

    /**
     * Create a new profile
     *
     * Example: Create profile
     *  $lxd->profiles->create(
     *      'test-profile',
     *      'My test profile',
     *      ["limits.memory" => "2GB"],
     *      [
     *          "kvm" => [
     *              "type" => "unix-char",
     *              "path" => "/dev/kvm"
     *          ],
     *      ]
     *  );
     *
     * @param  string $name        Name of profile
     * @param  string $description Description of profile
     * @param  array  $config      Configuration of profile
     * @param  array  $devices     Devices of profile
     * @return object
     */
    public function create($name, $description = '', $config = null, $devices = null)
    {
        $profile                = [];
        $profile['name']        = $name;
        $profile['description'] = $description;
        $profile['config']      = $config;
        $profile['devices']     = $devices;

        $response = $this->client->connection->post($this->client->endpoint, $profile);

        return $response->body->metadata;
    }

    /**
     * Update profile
     *
     * Example: Update profile
     *  $lxd->profiles->update(
     *      'test-profile',
     *      'My test profile',
     *      ["limits.memory" => "2GB"],
     *      [
     *          "kvm" => [
     *              "type" => "unix-char",
     *              "path" => "/dev/kvm"
     *          ],
     *      ]
     *  );
     *
     * @param  string $name        Name of profile
     * @param  string $description Description of profile
     * @param  array  $config      Configuration of profile
     * @param  array  $devices     Devices of profile
     * @return object
     */
    public function update($name, $description = '', $config = null, $devices = null)
    {
        $profile                = [];
        $profile['description'] = $description;
        $profile['config']      = $config;
        $profile['devices']     = $devices;

        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->patch($endpoint, $profile);

        return $response->body->metadata;
    }

    /**
     * Replace profile
     *
     * Example: Replace profile
     *  $profile = $lxd->profiles->info('test-profile');
     *  $profile->description = 'My test profile';
     *  $profile->config->{'limits.memory'} = '2GB';
     *  $profile->devices->kvm->type = 'unix-char';
     *  $profile->devices->kvm->path = '/dev/kvm';
     *
     *  $lxd->profiles->replace('test-profile', $profile);
     *
     * @param  string $name        Name of profile
     * @param  string $description Description of profile
     * @param  array  $config      Configuration of profile
     * @param  array  $devices     Devices of profile
     * @return object
     */
    public function replace($name)
    {
        // $profile                = [];
        // $profile['description'] = $description;
        // $profile['config']      = $config;
        // $profile['devices']     = $devices;

        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->put($endpoint, $profile);

        return $response->body->metadata;
    }

    /**
     * Rename profile
     *
     * @param  string $name    Name of profile
     * @param  string $newName Name of new profile
     * @return object
     */
    public function rename($name, $newName)
    {
        $profile                = [];
        $profile['name']        = $newName;

        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->post($endpoint, $profile);

        return $response->body->metadata;
    }

    /**
     * Delete a profile
     *
     * @param  string $name Name of profile
     */
    public function delete($name)
    {
        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->delete($endpoint);

        if ($response->body->status_code !== 200) {
            throw new \Exception('Profile not deleted: '.$response->body->error);
        }
    }
}
