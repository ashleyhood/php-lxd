<?php

namespace Opensaucesystems\Lxd\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Opensaucesystems\Lxd\Exception\AuthenticationFailedException;

/**
 * Handle LXD errors
 *
 */
class LxdExceptionThower implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $promise = $next($request);
        
        return $promise->then(function (ResponseInterface $response) use ($request) {
            return $response;
        }, function (\Exception $e) use ($request) {
            $response = $e->getResponse();

            if (403 === $response->getStatusCode()) {
                throw new AuthenticationFailedException($request, $response, $e);
            }
            
            throw $e;
        });
    }
}
