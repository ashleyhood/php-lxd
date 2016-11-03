<?php

namespace Opensaucesystems\Lxd\Endpoint;

use Opensaucesystems\Lxd\Client;
use Opensaucesystems\Lxd\HttpClient\Message\ResponseMediator;
use Opensaucesystems\Lxd\Exception\SourceImageException;

class Containers extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/containers/';
    }

    /**
     * List all containers on the server
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $containers = [];

        foreach ($this->get($this->getEndpoint()) as $container) {
            $containers[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $container);
        }

        return $containers;
    }

    /**
     * Get information on a container
     *
     * @param string $name Name of container
     * @return object
     */
    public function info($name)
    {
        return $this->get($this->getEndpoint().$name);
    }

    /**
     * Get the current state of the container
     *
     * @param string $name Name of container
     * @return object
     */
    public function state($name)
    {
        return $this->get($this->getEndpoint().$name.'/state');
    }

    /**
     * Change the state of the container
     *
     * @param  string $name     Name of container
     * @param  string $state    State change action (stop, start, restart, freeze or unfreeze)
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function setState($name, $state, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        $opts['action'] = $state;
        $opts['timeout'] = $timeout;
        $opts['force'] = $force;
        $opts['stateful'] = $stateful;

        $response = $this->put($this->getEndpoint().$name.'/state', $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Start the container
     *
     * @param  string $name Name of container
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function start($name, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        return $this->setState($name, 'start', $timeout, $force, $stateful, $wait);
    }

    /**
     * Stop the container
     *
     * @param  string $name Name of container
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function stop($name, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        return $this->setState($name, 'stop', $timeout, $force, $stateful, $wait);
    }

    /**
     * Restart the container
     *
     * @param  string $name Name of container
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function restart($name, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        return $this->setState($name, 'restart', $timeout, $force, $stateful, $wait);
    }

    /**
     * Freeze the container
     *
     * @param  string $name Name of container
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function freeze($name, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        return $this->setState($name, 'freeze', $timeout, $force, $stateful, $wait);
    }

    /**
     * Unfreeze the container
     *
     * @param  string $name Name of container
     * @param  int    $timeout  Time after which the operation is considered to have failed (default: no timeout)
     * @param  bool   $force    Whether to force the operation by killing the container
     * @param  bool   $stateful Whether to store/restore runtime state (only valid for stop and start, default: false)
     * @param  bool   $wait     Wait for operation to finish
     * @return object
     */
    public function unfreeze($name, $timeout = 30, $force = true, $stateful = false, $wait = false)
    {
        return $this->setState($name, 'unfreeze', $timeout, $force, $stateful, $wait);
    }

    /**
     * Create a container
     *
     * Create from an image (local or remote).  The container will
     * be created in the stopped state.
     *
     * Example: Create container from image specified by alias
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "alias" => "ubuntu/xenial/amd64",
     *      ]
     *  );
     *
     * Example: Create container from image specified by fingerprint
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "fingerprint" => "097e75d6f7419d3a5e204d8125582f2d7bdd4ee4c35bd324513321c645f0c415",
     *      ]
     *  );
     *
     * Example: Create container based on most recent match of image properties
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "properties" => [
     *              "os"           => "ubuntu",
     *              "release"      => "14.04",
     *              "architecture" => "x86_64",
     *          ],
     *      ]
     *  );
     *
     * Example: Create an empty container
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "empty" => true,
     *      ]
     *  );
     *
     * Example: Create container with custom configuration.
     *
     * # Set the MAC address of the container's eth0 device
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "alias"  => "ubuntu/xenial/amd64",
     *          "config" => [
     *              "volatile.eth0.hwaddr" => "aa:bb:cc:dd:ee:ff",
     *          ],
     *      ]
     *  );
     *
     * Example: Create container and apply profiles to it
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "alias"  => "ubuntu/xenial/amd64",
     *          "profiles" => ["migratable", "unconfined"],
     *      ]
     *  );
     *
     * Example: Create container from a publicly-accessible remote image
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "server" => "https://images.linuxcontainers.org:8443",
     *          "alias"  => "ubuntu/xenial/amd64",
     *      ]
     *  );
     *
     * Example: Create container from a private remote image (authenticated by a secret)
     *  $lxd->containers->create(
     *      "test",
     *      [
     *          "server" => "https://private.example.com:8443",
     *          "alias" => "ubuntu/xenial/amd64",
     *          "secret" => "my_secrect",
     *      ]
     *  );
     *
     * @param string $name The name of the container
     * @param array $options Options to create the container
     * @param bool $wait Wait for operation to finish
     * @return object
     */
    public function create($name, array $options, $wait = false)
    {
        $source = $this->getSource($options);

        if (empty($options['empty']) && empty($source)) {
            throw new SourceImageException();
        }

        if (!empty($options['empty'])) {
            $opts = $this->getEmptyOptions($name, $options);
        } elseif (!empty($options['server'])) {
            $opts = $this->getRemoteImageOptions($name, $source, $options);
        } else {
            $opts = $this->getLocalImageOptions($name, $source, $options);
        }

        $response = $this->post($this->getEndpoint(), $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Create a copy of an existing local container
     *
     * Example: Copy container
     *  $lxd->containers->copy('existing', 'new');
     *
     * Example: Copy container and apply profiles to it
     *  $lxd->containers->copy(
     *    'existing',
     *    'new',
     *    ['profiles' => ['default', 'public']
     *  );
     *
     * @param  string $name Name of existing container
     * @param  string $copyName Name of copied container
     * @param  array  $options Options for copied container
     * @param  bool   $wait Wait for operation to finish
     * @return object
     */
    public function copy($name, $copyName, array $options = [], $wait = false)
    {
        $opts = $this->getOptions($copyName, $options);

        $opts['source']['type'] = 'copy';
        $opts['source']['source'] = $name;

        $response = $this->post($this->getEndpoint(), $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Replace the configuration of a container
     *
     * Configuration is overwritten, not merged.  Accordingly, clients should
     * first call the info method to obtain the current configuration of a
     * container.  The resulting object should be modified and then passed to
     * the update method.
     *
     * Note that LXD does not allow certain attributes to be changed (e.g.
     * <code>status</code>, <code>status_code</code>, <code>stateful</code>,
     * <code>name</code>, etc.) through this call.
     *
     * Example: Change container to be ephemeral (i.e. it will be deleted when stopped)
     *  $container = $lxd->containers->show('test');
     *  $container->ephemeral = true;
     *  $lxd->containers->update('test', $container);
     *
     * @param string $name Name of container
     * @param object $container Container to update
     * @param bool $wait Wait for operation to finish
     * @return object
     */
    public function replace($name, $container, $wait = false)
    {
        $response = $this->put($this->getEndpoint().$name, $container);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Rename a container
     *
     * @param string $name    Name of existing container
     * @param string $newName Name of new container
     * @param bool   $wait    Wait for operation to finish
     * @return array
     */
    public function rename($name, $newName, $wait = false)
    {
        $opts['name'] = $newName;
        $response = $this->post($this->getEndpoint().$name, $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Delete a container
     *
     * @param string $name Name of container
     * @param bool   $wait Wait for operation to finish
     * @return array
     */
    public function remove($name, $wait = false)
    {
        $response = $this->delete($this->getEndpoint().$name);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
        }

        return $response;
    }

    /**
     * Execute a command in a container
     *
     * @param string       $name        Name of container
     * @param array|string $command     Command and arguments
     * @param bool         $record      Whether to store stdout and stderr
     * @param array        $environment An associative array, the key will be the environment variable name
     * @param bool         $wait        Wait for operation to finish
     * @return object
     */
    public function execute($name, $command, $record = false, array $environment = [], $wait = false)
    {
        if (is_string($command)) {
            $command = $this->split($command);
        }

        $opts['command'] = $command;

        if (!empty($environment)) {
            $opts['environment'] = $environment;
        }

        if ($record === true) {
            $opts['record-output'] = true;
        }

        $opts['wait-for-websocket'] = false;
        $opts['interactive'] = false;

        $response = $this->post($this->getEndpoint().$name.'/exec', $opts);

        if ($wait) {
            $response = $this->client->operations->wait($response['id']);
            
            $logs = [];
            $output = $response['metadata']['output'];
            $return = $response['metadata']['return'];
            unset($response);

            foreach ($output as $log) {
                $response['output'][] = str_replace(
                    '/'.$this->client->getApiVersion().'/containers/'.$name.'/logs/',
                    '',
                    $log
                );
            }
    
            $response['return'] = $return;
        }

        return $response;
    }

    public function __get($endpoint)
    {
        $class = __NAMESPACE__.'\\Containers\\'.ucfirst($endpoint);

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
     * @param array $options Options for creating container
     * @return array
     */
    private function getSource($options)
    {
        foreach (['alias', 'fingerprint', 'properties'] as $attr) {
            if (!empty($options[$attr])) {
                return [$attr => $options[$attr]];
            }
        }

        return [];
    }

    /**
     * Get the options for creating container
     *
     * @param string $name Name of container
     * @param array $options Options for creating container
     * @return array
     */
    private function getOptions($name, $options)
    {
        $only = [
            'architecture',
            'profiles',
            'ephemeral',
            'config',
            'devices',
        ];
        $opts         = array_intersect_key($options, array_flip((array) $only));
        $opts['name'] = $name;

        return $opts;
    }

    /**
     * Get options for creating an empty container
     *
     * @param string $name Name of container
     * @param array $options Options for creating container
     * @return array
     */
    private function getEmptyOptions($name, $options)
    {
        $attrs = [
            'alias',
            'fingerprint',
            'properties',
            'server',
            'secret',
            'protocol',
            'certificate',
        ];

        foreach ($attrs as $attr) {
            if (!empty($options[$attr])) {
                throw new \Exception('empty => true is not compatible with '.$attr);
            }
        }

        $opts                   = $this->getOptions($name, $options);
        $opts['source']['type'] = 'none';

        return $opts;
    }

    /**
     * Get options for creating a container from remote image
     *
     * @param string $name Name of container
     * @param array $source Source of the image
     * @param array $options Options for creating container
     * @return array
     */
    private function getRemoteImageOptions($name, $source, $options)
    {
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

        $opts                   = $this->getOptions($name, $options);
        $opts['source']         = array_merge($source, $remoteOptions);
        $opts['source']['type'] = 'image';
        $opts['source']['mode'] = 'pull';

        return $opts;
    }

    /**
     * Get options for creating a container from local image
     *
     * @param string $name Name of container
     * @param array $source Source of the image
     * @param array $options Options for creating container
     * @return array
     */
    private function getLocalImageOptions($name, $source, $options)
    {
        $attrs = [
            'secret',
            'protocol',
            'certificate',
        ];

        foreach ($attrs as $attr) {
            if (!empty($options[$attr])) {
                throw new \Exception('Only setting remote server is compatible with '.$attr);
            }
        }

        $opts                   = $this->getOptions($name, $options);
        $opts['source']         = $source;
        $opts['source']['type'] = 'image';

        return $opts;
    }

    /**
     * To split a string
     *
     * @param  string $string String to split into array
     * @return array
     */
    private function split($string)
    {
        $pattern = '/\s*(?>([^\s\\\'\"]+)|\'([^\']*)\'|"((?:[^\"\\\\]|\\.)*)"|(\\.?)|(\S))(\s|\z)?/';
        preg_match_all($pattern, $string, $matches);
        $words = [];

        foreach ($matches[0] as $value) {
            if (!empty($value)) {
                $words[] = trim(trim($value), '\'"');
            }
        }

        return $words;
    }
}
