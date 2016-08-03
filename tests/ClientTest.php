<?php

namespace Opensaucesystems\Lxd;

use Mockery;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->password       = 'Super secret password';
        $this->standardReturn = [
            'status_code' => 200,
            'body'        => [
                'type'        => 'sync',
                'status'      => 'Success',
                'status_code' => 200,
                'metadata'    => []
            ],
        ];
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    private function generateReturn(array $return)
    {
        return json_decode(json_encode($return));
    }

    /**
     * testInstanceOf
     * 
     * @covers Opensaucesystems\Lxd\Client::__construct
     */
    public function testInstanceOf()
    {
        $metadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $standardReturn                 = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $this->generateReturn($metadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $this->assertInstanceOf('Opensaucesystems\Lxd\Client', $client);
    }

    /**
     * Test setting client info
     * 
     * @covers Opensaucesystems\Lxd\Client::syncInfo
     */
    public function testSyncInfoOfLxdClient()
    {
        $metadata = json_decode(json_encode([
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ]));

        $standardReturn = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $metadata;

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($standardReturn);

        $client = new Client($mock);

        $info = $client->syncInfo();

        $this->assertObjectHasAttribute('api_extensions', $info);
        $this->assertObjectHasAttribute('api_status', $info);
        $this->assertObjectHasAttribute('api_version', $info);
        $this->assertObjectHasAttribute('auth', $info);
        $this->assertObjectHasAttribute('public', $info);
    }

    /**
     * Test untrusted connection to get info
     * 
     * @covers Opensaucesystems\Lxd\Client::info
     */
    public function testInfoForUntrusted()
    {
        $metadata = json_decode(json_encode([
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ]));

        $standardReturn                 = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $metadata;

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $info = $client->info();

        $this->assertObjectHasAttribute('api_extensions', $info);
        $this->assertObjectHasAttribute('api_status', $info);
        $this->assertObjectHasAttribute('api_version', $info);
        $this->assertObjectHasAttribute('auth', $info);
        $this->assertObjectHasAttribute('public', $info);

        $this->assertEquals($info->auth, 'untrusted');
    }

    /**
     * Test client is not trusted
     * 
     * @covers Opensaucesystems\Lxd\Client::trusted
     */
    public function testTrustedIsFalseForUnTrusted()
    {
        $metadata = json_decode(json_encode([
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ]));

        $standardReturn                 = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $metadata;

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $trusted = $client->trusted();
        
        $this->assertNotTrue($trusted);
    }

    /**
     * Test untrusted server config update
     * 
     * @depends testInstanceOf
     * @covers  Opensaucesystems\Lxd\Client::update
     * @expectedException \Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed
     */
    public function testUpdateUntrustedThrowsException($client)
    {
        $metadata = json_decode(json_encode([
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ]));

        $standardReturn                 = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $metadata;

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $config                                  = (object) [];
        $config->{'images.auto_update_interval'} = '24';
        $config->{'core.trust_password'}         = 'my-new-password';

        $client->update($config);
    }

    /**
     * Test trusted server config update
     * 
     * @covers Opensaucesystems\Lxd\Client::update
     */
    public function testUpdateSetAutoUpdateInterval()
    {
        $metadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
            'config' => [                                     # Host configuration
                'core.trust_password' => true,
                'core.https_address' => '[::]:8443'
            ],
            'environment' => [                                # Various information about the host (OS, kernel, ...)
                'addresses' => [
                    '1.2.3.4:8443',
                    '[1234::1234]:8443'
                ],
                'architectures' => [
                    'x86_64',
                    'i686'
                ],
                'certificate' => 'PEM certificate',
                'driver' => 'lxc',
                'driver_version' => '1.0.6',
                'kernel' => 'Linux',
                'kernel_architecture' => 'x86_64',
                'kernel_version' => '3.16',
                'server' => 'lxd',
                'server_pid' => 10224,
                'server_version' => '0.8.1',
                'storage' => 'btrfs',
                'storage_version' => '3.19',
            ],
        ];

        $standardReturn                = $this->generateReturn($this->standardReturn);
        $trustedReturn                 = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata = $this->generateReturn($metadata);

        $updateReturn                 = $this->generateReturn($this->standardReturn);
        $updateReturn->body->metadata = $this->generateReturn($metadata);

        $updateReturn->body->metadata->config->{'images.auto_update_interval'} = '24';

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $updateReturn);
        $mock->shouldReceive('put')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $config = $client->info()->config;

        $config->{'core.trust_password'}         = $this->password;
        $config->{'images.auto_update_interval'} = '24';

        $update = $client->update($config);

        $this->assertEquals('24', $update->{'images.auto_update_interval'});
    }

    /**
     * Test server config update failed
     * 
     * @covers  Opensaucesystems\Lxd\Client::update
     * @expectedException \Opensaucesystems\Lxd\Exception\ServerException
     */
    public function testUpdateConfigNotSavedThrowsException()
    {
        $metadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
            'config' => [                                     # Host configuration
                'core.trust_password' => true,
                'core.https_address' => '[::]:8443'
            ],
            'environment' => [                                # Various information about the host (OS, kernel, ...)
                'addresses' => [
                    '1.2.3.4:8443',
                    '[1234::1234]:8443'
                ],
                'architectures' => [
                    'x86_64',
                    'i686'
                ],
                'certificate' => 'PEM certificate',
                'driver' => 'lxc',
                'driver_version' => '1.0.6',
                'kernel' => 'Linux',
                'kernel_architecture' => 'x86_64',
                'kernel_version' => '3.16',
                'server' => 'lxd',
                'server_pid' => 10224,
                'server_version' => '0.8.1',
                'storage' => 'btrfs',
                'storage_version' => '3.19',
            ],
        ];

        $standardReturn                = $this->generateReturn($this->standardReturn);
        $trustedReturn                 = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata = $this->generateReturn($metadata);

        $standardReturn                    = $this->generateReturn($this->standardReturn);
        $standardReturn->body->type        = 'error';
        $standardReturn->body->error       = 'Failure';
        $standardReturn->body->status_code = 400;

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($trustedReturn);
        $mock->shouldReceive('put')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $config = $client->info()->config;
        $config->{'images.auto_update_interval'} = '24';
        $config->{'core.trust_password'}         = $this->password;

        $update = $client->update($config);
    }

    /**
     * Test that endpoint doesn't exist
     * 
     * @covers  Opensaucesystems\Lxd\Client::__get
     * @expectedException \Opensaucesystems\Lxd\Exception\EndpointException
     */
    public function test__GetExpectException()
    {
        $metadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $standardReturn                 = $this->generateReturn($this->standardReturn);
        $standardReturn->body->metadata = $this->generateReturn($metadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($standardReturn);

        $client = new Client($mock);

        $client->nonEndpoint;
    }
}
