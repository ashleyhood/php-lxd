<?php

namespace Opensaucesystems\Lxd\HttpClient\Plugin;

use Http\Promise\Promise;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Client\Exception\HttpException;
use Opensaucesystems\Lxd\Exception\OperationException;
use Opensaucesystems\Lxd\Exception\AuthenticationFailedException;
use Opensaucesystems\Lxd\Exception\NotFoundException;
use Opensaucesystems\Lxd\Exception\ConflictException;

/**
 * Handle LXD errors
 *
 */
class LxdExceptionThower implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $promise = $next($request);
        
        return $promise->then(function (ResponseInterface $response) use ($request) {
            return $response;
        }, function (\Exception $e) use ($request) {
            if (get_class($e) === HttpException::class) {
                $response = $e->getResponse();

                if (401 === $response->getStatusCode()) {
                    throw new OperationException($request, $response, $e);
                }

                if (403 === $response->getStatusCode()) {
                    throw new AuthenticationFailedException($request, $response, $e);
                }
                
                if (404 === $response->getStatusCode()) {
                    throw new NotFoundException($request, $response, $e);
                }
                
                if (409 === $response->getStatusCode()) {
                    throw new ConflictException($request, $response, $e);
                }
            }

            throw $e;
        });
    }
}
