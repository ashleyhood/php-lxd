<?php
namespace Opensaucesystems\Lxd\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    /**
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public static function getContent(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();

        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            $content = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if ($response->getStatusCode() >= 100 && $response->getStatusCode() <= 111) {
                    return $content;
                }

                return $content['metadata'];
            }
        }

        return $body;
    }
    
    /**
     * Get the value for a single header
     * @param ResponseInterface $response
     * @param string $name
     *
     * @return string|null
     */
    public static function getHeader(ResponseInterface $response, $name)
    {
        $headers = $response->getHeader($name);
        return array_shift($headers);
    }
}
