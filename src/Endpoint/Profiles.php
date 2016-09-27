<?php

namespace Opensaucesystems\Lxd\Endpoint;

class Profiles extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/profiles/';
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

        foreach ($this->get($this->getEndpoint()) as $profile) {
            $profiles[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $profile);
        }

        return $profiles;
    }

    /**
     * Show information on a profile
     *
     * @param  string $name name of profile
     * @return object
     */
    public function info($name)
    {
        return $this->get($this->getEndpoint().$name);
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
    public function create($name, $description = '', array $config = null, array $devices = null)
    {
        $profile                = [];
        $profile['name']        = $name;
        $profile['description'] = $description;
        $profile['config']      = $config;
        $profile['devices']     = $devices;

        return $this->post($this->getEndpoint(), $profile);
    }

    /**
     * Update profile.
     * This will only update supplied profile settings and leave the other settings
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
    public function update($name, $description = '', array $config = null, array $devices = null)
    {
        $profile                = [];
        $profile['description'] = $description;
        $profile['config']      = $config;
        $profile['devices']     = $devices;

        return $this->patch($this->getEndpoint().$name, $profile);
    }

    /**
     * Replace profile.
     * This will replace all the profile settings with the supplied settings
     *
     * Example: Replace profile
     *  $lxd->profiles->replace(
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
    public function replace($name, $description = '', array $config = null, array $devices = null)
    {
        $profile                = [];
        $profile['description'] = $description;
        $profile['config']      = $config;
        $profile['devices']     = $devices;

        return $this->put($this->getEndpoint().$name, $profile);
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

        return $this->post($this->getEndpoint().$name, $profile);
    }

    /**
     * Delete a profile
     *
     * @param  string $name Name of profile
     */
    public function remove($name)
    {
        return $this->delete($this->getEndpoint().$name);
    }
}
