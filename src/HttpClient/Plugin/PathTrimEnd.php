<?php
namespace Opensaucesystems\Lxd\HttpClient\Plugin;

use Http\Promise\Promise;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

/**
 * Prepend the URI with a string.
 *
 */
class PathTrimEnd implements Plugin
{
    private $trim;

    /**
     * @param string $trim
     */
    public function __construct($trim = '/')
    {
        $this->trim = $trim;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $trimPath = rtrim($request->getUri()->getPath(), $this->trim);
        $uri = $request->getUri()->withPath($trimPath);
        $request = $request->withUri($uri);

        return $next($request);
    }
}
