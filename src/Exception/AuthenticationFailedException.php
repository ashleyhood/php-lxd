<?php

namespace Opensaucesystems\Lxd\Exception;

use Http\Client\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationFailedException extends HttpException
{
    protected $message = 'LXD client certificate is not trusted.';
    
    public function __construct(RequestInterface $request, ResponseInterface $response, \Exception $previous = null)
    {
        parent::__construct($this->message, $request, $response, $previous);
    }
}
