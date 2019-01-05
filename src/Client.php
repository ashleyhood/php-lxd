<?php

namespace Opensaucesystems\Lxd;

use Opensaucesystems\Lxd\Exception\InvalidEndpointException;
use Opensaucesystems\Lxd\Exception\ClientConnectionException;
use Opensaucesystems\Lxd\Exception\ServerException;
use Opensaucesystems\Lxd\HttpClient\Plugin\PathPrepend;
use Opensaucesystems\Lxd\HttpClient\Plugin\PathTrimEnd;
use Opensaucesystems\Lxd\HttpClient\Plugin\LxdExceptionThower;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;

class Client
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * The object that sends HTTP messages
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * A HTTP client with all our plugins
     *
     * @var PluginClient
     */
    private $pluginClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Plugin[]
     */
    private $plugins = [];

    /**
     * True if we should create a new Plugin client at next request.
     *
     * @var bool
     */
    private $httpClientModified = true;

    /**
     * Create a new lxd client Instance
     */
    public function __construct(
        HttpClient $httpClient = null,
        $apiVersion = null,
        $url = null,
        string $projectName = "default"
    ) {
        $this->httpClient     = $httpClient ?: HttpClientDiscovery::find();
        $this->messageFactory = MessageFactoryDiscovery::find();
        $this->apiVersion     = $apiVersion ?: '1.0';
        $this->url            = $url ?: 'https://127.0.0.1:8443';
        $this->projectName    = $projectName;

        $this->addPlugin(new LxdExceptionThower());

        $this->setUrl($this->url);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the URL of your LXD instance.
     *
     * @param string $url URL of the API in the form of https://hostname:port
     */
    public function setUrl($url)
    {
        $this->url = $url;

        $this->removePlugin(Plugin\AddHostPlugin::class);
        $this->removePlugin(PathPrepend::class);
        $this->removePlugin(PathTrimEnd::class);

        $this->addPlugin(new Plugin\AddHostPlugin(UriFactoryDiscovery::find()->createUri($this->url)));
        $this->addPlugin(new PathPrepend(sprintf('/%s', $this->getApiVersion())));
        $this->addPlugin(new PathTrimEnd());
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param string $fqcn
     */
    public function removePlugin($fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->httpClientModified = true;
            }
        }
    }

    /**
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;

            $this->pluginClient = new HttpMethodsClient(
                new PluginClient($this->httpClient, $this->plugins),
                $this->messageFactory
            );
        }
        return $this->pluginClient;
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClientModified = true;
        $this->httpClient = $httpClient;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param CacheItemPoolInterface $cache
     * @param array                  $config
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = [])
    {
        $this->removeCache();
        $this->addPlugin(new Plugin\CachePlugin($cachePool, $this->streamFactory, $config));
    }

    /**
     * Remove the cache plugin
     */
    public function removeCache()
    {
        $this->removePlugin(Plugin\CachePlugin::class);
    }

    public function __get($endpoint)
    {
        $class = __NAMESPACE__.'\\Endpoint\\'.ucfirst($endpoint);

        if (class_exists($class)) {
            return new $class($this);
        } else {
            throw new InvalidEndpointException(
                'Endpoint '.$class.', not implemented.'
            );
        }
    }

    /**
     * Make sure to move the cache plugin to the end of the chain
     */
    private function pushBackCachePlugin()
    {
        $cachePlugin = null;
        foreach ($this->plugins as $i => $plugin) {
            if ($plugin instanceof Plugin\CachePlugin) {
                $cachePlugin = $plugin;
                unset($this->plugins[$i]);
                $this->plugins[] = $cachePlugin;
                return;
            }
        }
    }
    /**
     * Set the project to use on the server
     * @param string $projectName The project name to use
     */
    public function setProject(string $projectName)
    {
        $this->project = $projectName;
    }
    /**
     * Get the project using on the client
     * @return string The current project
     */
    public function getProject()
    {
        return $this->project;
    }
}
