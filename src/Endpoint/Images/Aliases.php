<?php

namespace Opensaucesystems\Lxd\Endpoint\Images;

use Opensaucesystems\Lxd\Endpoint\AbstructEndpoint;

class Aliases extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/images/aliases/';
    }

    /**
     * List of alias for an image
     *
     * @return array
     */
    public function all()
    {
        $aliases = [];

        $config = [
            "project"=>$this->client->getProject()
        ];

        foreach ($this->get($this->getEndpoint(), $config) as $alias) {
            $aliases[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $alias);
        }

        return $aliases;
    }

    /**
     * Get information on an alias
     *
     * @param string $name      Name of container
     * @return object
     */
    public function info($name)
    {
        $config = [
            "project"=>$this->client->getProject()
        ];

        return $this->get($this->getEndpoint().$name, $config);
    }

    /**
     * Create an alias of an image
     *
     * @param  string $fingerprint Fingerprint of image
     * @param  string $aliasName   Name of alias
     * @param  string $description Description of alias
     */
    public function create($fingerprint, $aliasName, $description = '')
    {
        $opts['target']      = $fingerprint;
        $opts['name']        = $aliasName;
        $opts['description'] = $description;

        $config = [
            "project"=>$this->client->getProject()
        ];

        return $this->post($this->getEndpoint(), $opts, $config);
    }

    /**
     * Replace an image alias
     *
     * Example: Replace alias "ubuntu/xenial/amd64" to point to image "097..."
     *  $lxd->images->aliases->update(
     *      'test',
     *      'd02d6cf5a494df1c88144c7cbfec47b6d010a79baf18975a7c17abbf31cbae40',
     *      'new description'
     *  );
     *
     * @param  string $name        Name of alias
     * @param  string $fingerprint Fingerprint of image
     * @param  string $description Description of alias
     * @return object
     */
    public function replace($name, $fingerprint, $description = '')
    {
        $opts['target']      = $fingerprint;
        $opts['description'] = $description;

        $config = [
            "project"=>$this->client->getProject()
        ];

        return $this->put($this->getEndpoint().$name, $opts, $config);
    }

    /**
     * Rename an alias
     *
     * @param  string $name    Name of container
     * @param  string $newName Name of new alias
     * @return object
     */
    public function rename($name, $newName)
    {
        $opts['name'] = $newName;

        $config = [
            "project"=>$this->client->getProject()
        ];

        return $this->post($this->getEndpoint().$name, $opts, $config);
    }

    /**
     * Delete an alias
     *
     * @param  string $name Name of alias
     * @return object
     */
    public function remove($name)
    {
        $config = [
            "project"=>$this->client->getProject()
        ];

        return $this->delete($this->getEndpoint().$name, $config);
    }
}
