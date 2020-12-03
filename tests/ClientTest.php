<?php

namespace Opensaucesystems\Lxd\Tests;

use Mockery;
use Opensaucesystems\Lxd\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function tearDown(): void
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

    public function testInvalidEndpointException()
    {
        $httpClient = Mockery::mock('\Http\Client\HttpClient');

        $client = new Client($httpClient);

        $this->expectException(\Opensaucesystems\Lxd\Exception\InvalidEndpointException::class);

        $client->nonEndpoint;
    }
}
