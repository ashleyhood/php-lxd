<?php

namespace Opensaucesystems\Lxd\Endpoint;

use Opensaucesystems\Lxd\Exception\InvalidEndpointException;

class Images extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/images/';
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

        foreach ($this->get($this->getEndpoint()) as $image) {
            $images[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $image);
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
        $endpoint = $this->getEndpoint().$fingerprint;

        if (!empty($secret)) {
            $endpoint .= '?secret='.$secret;
        }

        return $this->get($endpoint);
    }

    /**
     * Create and publish a new image
     *
     * Ways to create an image:
     * @todo Standard http file upload
     * # Source image (transfers a remote image)
     * # Source container (makes an image out of a local container)
     * @todo Remote image URL (downloads a remote image)
     *
     * @param  array $options Options to create the image
     * @param  bool  $wait Wait for operation to finish
     * @return object
     */
    public function create(array $options, $headers = [], $wait = false)
    {
        $response = $this->post($this->getEndpoint(), $options, $headers);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Import an image from a remote server
     *
     * Example: Import an image by alias
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
     *          "alias"  => "ubuntu/xenial/amd64",
     *      ]
     *  );
     *
     * Example: Import an image by fingerprint
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
     *          "fingerprint" => "65df07147e458f356db90fa66d6f907a164739b554a40224984317eee729e92a",
     *      ]
     *  );
     *
     * Example: Import image and automatically update it when it is updated on the remote server
     *  $lxd->images->createFromRemote(
     *      "https://images.linuxcontainers.org:8443",
     *      [
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
            'secret',
            'protocol',
            'certificate',
        ];
        $remoteOptions = array_intersect_key($options, array_flip((array) $only));

        $opts                     = $this->getOptions($options);
        $opts['auto_update']      = $autoUpdate;
        $opts['source']           = array_merge($source, $remoteOptions);
        $opts['source']['type']   = 'image';
        $opts['source']['mode']   = 'pull';
        $opts['source']['server'] = $server;

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
     * Replace the configuration of a image
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
     * Example: Change image to be public
     *  $image = $lxd->images->info('65df07147e458f356db90fa66d6f907a164739b554a40224984317eee729e92a');
     *  $image['public'] = true;
     *  $lxd->images->replace('test', $image);
     *
     * @param  string $fingerprint  Fingerprint of image
     * @param  array  $options      Options to replace
     * @param  bool   $wait         Wait for operation to finish
     * @return array
     */
    public function replace($fingerprint, $options, $wait = false)
    {
        $response = $this->put($this->getEndpoint().$fingerprint, $options);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Delete an image
     *
     * @param  string $fingerprint Fingerprint of image
     * @param  bool   $wait        Wait for operation to finish
     * @return array
     */
    public function remove($fingerprint, $wait = false)
    {
        $response = $this->delete($this->getEndpoint().$fingerprint);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    public function __get($endpoint)
    {
        $class = __NAMESPACE__.'\\Images\\'.ucfirst($endpoint);

        if (class_exists($class)) {
            return new $class($this->client);
        } else {
            throw new InvalidEndpointException(
                'Endpoint '.$class.', not implemented.'
            );
        }
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

        throw new \Exception('Alias or Fingerprint must be set');
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
            'auto_update',
        ];
        $opts = array_intersect_key($options, array_flip((array) $only));

        return $opts;
    }
}
