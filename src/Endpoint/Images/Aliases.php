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
        $response = $this->get($this->getEndpoint());

        foreach ($response as $alias) {
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
    public function show($name)
    {
        return $this->get($this->getEndpoint().$name);
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

        return $this->post($this->getEndpoint(), $opts);
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

        return $this->put($this->getEndpoint().$name, $opts);
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

        return $this->post($this->getEndpoint().$name, $opts);
    }

    /**
     * Delete an alias
     *
     * @param  string $name Name of alias
     * @return object
     */
    public function remove($name)
    {
        return $this->delete($this->getEndpoint().$name);
    }
}
