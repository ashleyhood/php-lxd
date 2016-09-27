<?php

namespace Opensaucesystems\Lxd\Endpoint;

class Certificates extends AbstructEndpoint
{
    protected function getEndpoint()
    {
        return '/certificates/';
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

        foreach ($this->get($this->getEndpoint()) as $certificate) {
            $certificates[] = str_replace('/'.$this->client->getApiVersion().$this->getEndpoint(), '', $certificate);
        }

        return $certificates;
    }

    /**
     * Show information on a certificate
     *
     * @param  string $fingerprint Fingerprint of certificate
     * @return object
     */
    public function info($fingerprint)
    {
        return $this->get($this->getEndpoint().$fingerprint);
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

        $response = $this->post($this->getEndpoint(), $options);

        return $fingerprint;
    }

    /**
     * Remove a trusted certificate
     *
     * @param  string $fingerprint Fingerprint of certificate
     */
    public function remove($fingerprint)
    {
        $this->delete($this->getEndpoint().$fingerprint);
    }
}
