<?php

namespace Opensaucesystems\Lxd\Client\Images;

class Aliases
{
    public function __construct($client)
    {
        $this->client = $client;
        $this->endpoint = $this->client->endpoint.'/aliases/';

        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }
    }

    /**
     * Get information on an alias
     *
     * Get information on an alias or when the name parameter is an
     * empty string, return an array of aliases
     *
     * @param  string $name      Name of image
     * @return mixed
     */
    public function get($name = '')
    {
        $endpoint = $this->endpoint.$name;
        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
    }

    /**
     * List of alias for an image
     *
     * @return array
     */
    public function all()
    {
        $aliases = [];
        $response = $this->get();

        foreach ($response as $alias) {
            $aliases[] = str_replace($this->endpoint, '', strstr($alias, $this->endpoint));
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
        if (empty($name)) {
            throw new \Exception('Missing alias name');
        }

        return $this->get($name);
    }

    /**
     * Create an alias of an image
     *
     * @param  string $fingerprint Fingerprint of image
     * @param  string $aliasName   Name of alias
     * @param  string $description Description of alias
     * @param  bool   $wait        Wait for operation to finish
     * @return object
     */
    public function create($fingerprint, $aliasName, $description = '')
    {
        $opts['target']      = $fingerprint;
        $opts['name']        = $aliasName;
        $opts['description'] = $description;

        $endpoint = $this->client->endpoint.'/aliases';
        $response = $this->client->connection->post($endpoint, $opts);

        return $response->body->metadata;
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
    public function update($name, $fingerprint, $description)
    {
        $opts['target']      = $fingerprint;
        $opts['description'] = $description;

        $endpoint = $this->client->endpoint.'/aliases/'.$name;
        $response = $this->client->connection->put($endpoint, $opts);

        return $response->body->metadata;
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
        $endpoint = $this->client->endpoint.'/aliases/'.$name;
        $response = $this->client->connection->post($endpoint, $opts);

        return $response->body->metadata;
    }

    /**
     * Delete an alias
     *
     * @param  string $name Name of alias
     * @return object
     */
    public function delete($name)
    {
        $endpoint = $this->client->endpoint.'/aliases/'.$name;
        $response = $this->client->connection->delete($endpoint);

        return $response->body->metadata;
    }
}
