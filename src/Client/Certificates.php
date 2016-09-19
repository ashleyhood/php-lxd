<?php

namespace Opensaucesystems\Lxd\Client;

use Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed;

class Certificates
{
    /**
     * A LXD Certificate
     */
    public function __construct($client)
    {
        $this->client = $client;
        $this->endpoint = $this->client->endpoint.'/';
    }

    /**
     * Get information on a trusted certificate
     *
     * Get information on a trusted certificate or when the
     * fingerprint parameter is an empty string,
     * return an array of all the
     * trusted certificate
     *
     * @param  string $fingerprint fingerprint of trusted certificate
     * @return mixed
     */
    public function get($fingerprint = '')
    {
        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }

        $endpoint = $this->endpoint.$fingerprint;
        $response = $this->client->connection->get($endpoint);

        return $response->body->metadata;
    }

    /**
     * List all trusted certificates on the server
     *
     * This is an alias of the get method with an empty string as the parameter
     *
     * @return array
     */
    public function all()
    {
        $certificates = [];
        $response = $this->get();

        foreach ($response as $certificate) {
            $certificates[] = str_replace($this->endpoint, '', strstr($certificate, $this->endpoint));
        }

        return $certificates;
    }

    /**
     * Get information on a certificate
     *
     * @param  string $fingerprint Fingerprint of certificate
     * @return object
     */
    public function info($fingerprint)
    {
        if (empty($fingerprint)) {
            throw new \Exception('Missing certificate fingerprint');
        }

        return $this->get($fingerprint);
    }

    /**
     * Add a new trusted certificate to the server
     *
     * Example: Add trusted certificate
     *  $lxd->certificates->add(file_get_contents('/tmp/lxd_client.crt'));
     *
     * Example: Add trusted certificate from untrusted client
     *  $lxd->certificates->add(file_get_contents('/tmp/lxd_client.crt'), 'secret');
     *
     * @param  string $certificate Certificate contents in PEM format
     * @param  string $password    Password for untrusted client
     * @param  string $name        Name for the certificate. If nothing is provided, the host in the TLS header for
     *                             the request is used.
     * @return string fingerprint of certificate
     */
    public function add($certificate, $password = null, $name = null)
    {
        // Convert PEM certificate to DER certificate
        $begin = "CERTIFICATE-----";
        $end   = "-----END";
        $pem_data = substr($certificate, strpos($certificate, $begin)+strlen($begin));
        $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
        $der = base64_decode($pem_data);

        $fingerprint = hash('sha256', $der);

        $options = [];
        $options['type'] = 'client';
        $options['certificate'] = base64_encode($der);

        if ($password !== null) {
            $options['password'] = $password;
        }

        if ($name !== null) {
            $options['name'] = $name;
        }

        $response = $this->client->connection->post($this->client->endpoint, $options);

        if ($response->body->status_code !== 200) {
            throw new \Exception('Certificate not added: '.$response->body->error);
        }

        return $fingerprint;
    }

    /**
     * Delete a trusted certificate
     *
     * @param  string $fingerprint Fingerprint of certificate
     */
    public function delete($fingerprint)
    {
        if (!$this->client->trusted()) {
            throw new ClientAuthenticationFailed();
        }

        if (empty($fingerprint)) {
            throw new \Exception('Missing certificate fingerprint');
        }

        $endpoint = $this->endpoint.$fingerprint;
        $response = $this->client->connection->delete($endpoint);

        if ($response->body->status_code !== 200) {
            throw new \Exception('Certificate not deleted: '.$response->body->error);
        }
    }
}
