<?php

namespace Opensaucesystems\Lxd;

use Httpful\Request;
use Opensaucesystems\Lxd\Exception\EndpointException;
use Opensaucesystems\Lxd\Exception\ClientConnectionException;
use Opensaucesystems\Lxd\Exception\NotFoundException;

class Connection
{
    /**
     * Create a new lxd connection
     */
    public function __construct($uri, $cert, $key, $version = '1.0', $verify = true)
    {
        $this->uri    = rtrim($uri, '/').'/'.$version;
        $this->cert   = $cert;
        $this->key    = $key;
        $this->verify = $verify;
    }

    /**
     * HTTP GET
     *
     * @param  string $endpoint API endpoint
     * @param  array  $headers  Headers in the form of an assoc array
     * @return object
     */
    public function get($endpoint = null, array $headers = [])
    {
        $url = rtrim($this->uri.'/'.$endpoint, '/');

        $request = Request::get($url);

        if (!empty($headers)) {
            $request->addHeaders($headers);
        }

        return $this->send($request);
    }

    /**
     * HTTP PUT
     *
     * @param  string $endpoint API endpoint
     * @param  mixed  $payload  Data to send
     * @param  array  $headers  Headers in the form of an assoc array
     * @return object
     */
    public function put($endpoint, $payload, $headers = [])
    {
        $url = rtrim($this->uri.'/'.$endpoint, '/');

        $request = Request::put($url)->body($payload);

        if (!empty($headers)) {
            $request->addHeaders($headers);
        }

        return $this->send($request);
    }

    /**
     * HTTP PATCH
     *
     * @param  string $endpoint API endpoint
     * @param  mixed  $payload  Data to send
     * @param  array  $headers  Headers in the form of an assoc array
     * @return object
     */
    public function patch($endpoint, $payload, $headers = [])
    {
        $url = rtrim($this->uri.'/'.$endpoint, '/');

        $request = Request::patch($url.'/')->body($payload);

        if (!empty($headers)) {
            $request->addHeaders($headers);
        }

        return $this->send($request);
    }

    /**
     * HTTP POST
     *
     * @param  string $endpoint API endpoint
     * @param  mixed  $payload  Data to send
     * @param  array  $headers  Headers in the form of an assoc array
     * @return object
     */
    public function post($endpoint, $payload, $headers = [])
    {
        $url = rtrim($this->uri.'/'.$endpoint, '/');

        $request = Request::post($url)->body($payload);

        if (!empty($headers)) {
            $request->addHeaders($headers);
        }

        return $this->send($request);
    }

    /**
     * HTTP DELETE
     *
     * @param  string $endpoint API endpoint
     * @param  array  $headers  Headers in the form of an assoc array
     * @return object
     */
    public function delete($endpoint, $headers = [])
    {
        $url = rtrim($this->uri.'/'.$endpoint, '/');

        $request = Request::delete($url);

        if (!empty($headers)) {
            $request->addHeaders($headers);
        }

        return $this->send($request);
    }

    /**
     * Send HTTP request
     * 
     * @param object $request Httpful\Request object
     * @return object
     */
    private function send(Request $request)
    {
        $request->sendsJson()
            ->authenticateWithCert(
                $this->cert,
                $this->key
            )
            ->strictSSL($this->verify);

        $result = $request->send();

        $response = (object) [];
        $response->status_code = $result->code;
        $response->headers = $result->headers;
        $response->body = $result->body;

        if (isset($response->body->type)) {
            if ($response->body->type === 'error' && $response->body->error_code === 404) {
                throw new NotFoundException('Not found: '.$response->body);
            }

            if ($response->body->type === 'sync' && $response->body->status_code !== 200) {
                throw new ClientConnectionException();
            }
        }

        return $response;
    }
}
