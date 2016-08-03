<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Exception\SourceImageException;

class Images extends AbstructEndpoint
{
    /**
     * A LXD image
     */
    public function __construct($client)
    {
        $this->client   = $client;
        $this->endpoint = $this->client->endpoint.'/';

        parent::__construct($client, __CLASS__);
    }

    /**
     * Get information on an image
     *
     * Get information on an image or when the fingerprint parameter is an
     * empty string, return an array of all the images
     *
     * @param  string $fingerprint Fingerprint of image
     * @return mixed
     */
    public function get($fingerprint = '', $secret = '')
    {
        $endpoint = $this->endpoint.$fingerprint;

        if (!empty($secret)) {
            $endpoint .= '?secret=#'.$secret;
        }

        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
    }

    /**
     * List all images on the server
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $images = [];
        $response = $this->get();

        foreach ($response as $image) {
            $images[] = str_replace($this->endpoint, '', strstr($image, $this->endpoint));
        }

        return $images;
    }

    /**
     * Get information on an image
     *
     * @param  string $fingerprint Fingerprint of image
     * @param  string $secret Secret to access private image by untrusted client
     * @return object
     */
    public function info($fingerprint, $secret = null)
    {
        if (empty($fingerprint)) {
            throw new \Exception('Missing image fingerprint');
        }

        return $this->get($fingerprint);
    }

    /**
     * Create and publish a new image
     *
     * Ways to create an image:
     * @todo Standard http file upload
     * # Source image dictionary (transfers a remote image)
     * # Source image dictionary (makes an image out of a local image)
     * @todo Remote image URL dictionary (downloads a remote image)
     *
     * @param  array $options Options to create the image
     * @param  bool  $wait Wait for operation to finish
     * @return object
     */
    public function create(array $options, $headers = [], $wait = false)
    {
        $response = $this->client->connection->post($this->client->endpoint, $options, $headers);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }

    /**
     * Import an image from a remote server
     *
     * Example: Import an image by alias
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
     *          "server" => "https://images.linuximages.org:8443",
     *          "alias"  => "ubuntu/xenial/amd64",
     *      ]
     *  );
     *
     * Example: Import an image by fingerprint
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
     *          "server"      => "https://images.linuximages.org:8443",
     *          "fingerprint" => "65df07147e458f356db90fa66d6f907a164739b554a40224984317eee729e92a",
     *      ]
     *  );
     *
     * Example: Import image and automatically update it when it is updated on the remote server
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
     *          "server"      => "https://images.linuximages.org:8443",
     *          "alias"  => "ubuntu/xenial/amd64",
     *      ],
     *      true
     *  );
     *
     * @param  string $server     Remote server
     * @param  array  $options    Options to create the image
     * @param  bool   $autoUpdate Whether or not the image should be automatically updated from the remote server
     * @param  bool   $wait       Wait for operation to finish
     * @return object
     */
    public function createFromRemote($server, array $options, $autoUpdate = false, $wait = false)
    {
        $source = $this->getSource($options);

        if (isset($options['protocol']) && !in_array($options['protocol'], ['lxd', 'simplestreams'])) {
            throw new \Exception('Invalid protocol.  Valid choices: lxd, simplestreams');
        }

        $only = [
            'server',
            'secret',
            'protocol',
            'certificate',
        ];
        $remoteOptions = array_intersect_key($options, array_flip((array) $only));

        $opts                   = $this->getOptions($options);
        $opts['auto_update']    = $autoUpdate;
        $opts['source']         = array_merge($source, $remoteOptions);
        $opts['source']['type'] = 'image';
        $opts['source']['mode'] = 'pull';

        return $this->create($opts, [], $wait);
    }

    /**
     * Create an image from a container
     *
     * Example: Create a private image from container
     *  $lxd->images->createFromContainer("container_name");
     *
     * Example: Create a public image from container
     *  $lxd->images->createFromContainer(
     *      "container_name",
     *      [
     *          "public" => true,
     *      ]
     *  );
     *
     * Example: Store properties with the new image, and override its filename
     *  $lxd->images->createFromContainer(
     *      "container_name",
     *      [
     *          "filename"   => "ubuntu-trusty.tar.gz",
     *          "properties" => ["os" => "Ubuntu"],
     *      ]
     *  );
     *
     * @param  string $name    The name of the container
     * @param  array  $options Options to create the container
     * @param  bool   $wait    Wait for operation to finish
     * @return object
     */
    public function createFromContainer($name, array $options, $wait = false)
    {
        $opts                   = $this->getOptions($options);
        $opts['source']['type'] = 'container';
        $opts['source']['name'] = $name;

        return $this->create($opts, [], $wait);
    }

    /**
     * Create an image from a snapshot
     *
     * Example: Create a private image from snapshot
     *  $lxd->images->createFromSnapshot("container_name", "snapshot_name");
     *
     * Example: Create a public image from snapshot
     *  $lxd->images->createFromContainer(
     *      "container_name",
     *      "snapshot_name",
     *      [
     *          "public" => true,
     *      ]
     *  );
     *
     * Example: Store properties with the new image, and override its filename
     *  $lxd->images->createFromContainer(
     *      "container_name",
     *      "snapshot_name",
     *      [
     *          "filename"   => "ubuntu-trusty.tar.gz",
     *          "properties" => ["os" => "Ubuntu"],
     *      ]
     *  );
     *
     * @param  string $container The name of the container
     * @param  string $snapshot  The name of the snapshot
     * @param  array  $options   Options to create the container
     * @param  bool   $wait      Wait for operation to finish
     * @return object
     */
    public function createFromSnapshot($container, $snapshot, array $options, $wait = false)
    {
        $opts                   = $this->getOptions($options);
        $opts['source']['type'] = 'snapshot';
        $opts['source']['name'] = $container.'/'.$snapshot;

        return $this->create($opts, [], $wait);
    }

    /**
     * Update the configuration of a image
     *
     * Configuration is overwritten, not merged.  Accordingly, clients should
     * first call the info method to obtain the current configuration of a
     * image.  The resulting object should be modified and then passed to
     * the update method.
     *
     * Note that LXD does not allow certain attributes to be changed (e.g.
     * <code>status</code>, <code>status_code</code>, <code>stateful</code>,
     * <code>name</code>, etc.) through this call.
     *
     * Example: Change image to be ephemeral (i.e. it will be deleted when stopped)
     *  $image = $lxd->images->info('65df07147e458f356db90fa66d6f907a164739b554a40224984317eee729e92a');
     *  $image->public = true;
     *  $lxd->images->update('test', $image);
     *
     * @param  string $fingerprint  Fingerprint of image
     * @param  object $image        Image to update
     * @param  bool   $wait         Wait for operation to finish
     * @return object
     */
    public function update($fingerprint, $image, $wait = false)
    {
        $endpoint = $this->endpoint.$fingerprint;
        $response = $this->client->connection->put($endpoint, $image);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }

    /**
     * Delete an image
     *
     * @param  string $fingerprint Fingerprint of image
     * @param  bool   $wait        Wait for operation to finish
     * @return array
     */
    public function delete($fingerprint, $wait = false)
    {
        $endpoint = $this->endpoint.$fingerprint;
        $response = $this->client->connection->delete($endpoint);

        if ($wait) {
            $response = $this->client->operations->wait($response->body->metadata->id);
        }

        return $response->body->metadata;
    }

    /**
     * Get image source attribute
     *
     * @param  array $options Options for creating image
     * @return array
     */
    private function getSource($options)
    {
        foreach (['alias', 'fingerprint'] as $attr) {
            if (!empty($options[$attr])) {
                return [$attr => $options[$attr]];
            }
        }

        return [];
    }

    /**
     * Get the options for creating image
     *
     * @param  string $name Name of image
     * @param  array  $options Options for creating image
     * @return array
     */
    private function getOptions($options)
    {
        $only = [
            'filename',
            'public',
            'properties',
        ];
        $opts = array_intersect_key($options, array_flip((array) $only));

        return $opts;
    }
}
