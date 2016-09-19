<?php

namespace Opensaucesystems\Lxd;

use Mockery;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * testInstanceOf
     *
     */
    public function testInstanceWithoutHttpClientPassInConstructor()
    {
        $client = new Client();

        $this->assertInstanceOf('\Http\Client\HttpClient', $client->getHttpClient());
    }

    /**
     * testInstanceOf
     *
     */
    public function testInstanceWithHttpClientPassInConstructor()
    {
        $httpClient = Mockery::mock('\Http\Client\HttpClient');

        $client = new Client($httpClient);

        $this->assertInstanceOf('\Http\Client\HttpClient', $client->getHttpClient());
    }

    /**
     * @test
     */
    public function testSetHttpClient()
    {
        $client = new Client(Mockery::mock('\Http\Client\HttpClient'));

        $httpClient = new \Http\Mock\Client();

        $client->setHttpClient($httpClient);

        $this->assertInstanceOf('\Http\Client\HttpClient', $client->getHttpClient());
    }

    /**
     * @test
     */
    public function testSetUrl()
    {
        $url = 'https://lxd.example.com:8443';

        $httpClient = Mockery::mock('\Http\Client\HttpClient');

        $client = new Client($httpClient);

        $client->setUrl($url);

        $this->assertEquals($url, $client->getUrl());
    }

    /**
     * Test that endpoint doesn't exist
     *
     * @expectedException \Opensaucesystems\Lxd\Exception\InvalidEndpointException
     */
    public function testInvalidEndpointException()
    {
        $httpClient = Mockery::mock('\Http\Client\HttpClient');

        $client = new Client($httpClient);

        $client->nonEndpoint;
    }
}
