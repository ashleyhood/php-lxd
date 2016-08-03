<?php

namespace Opensaucesystems\Lxd;

use Mockery;

class ProfilesTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
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
     * Test getting profile by name when client is untrusted
     * 
     * @expectedException \Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed
     */
    public function testGetNetworkByNameUntrusted()
    {
        $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->once()->andReturn($trustedReturn);

        $client = new Client($mock);

        $network = $client->profiles->info('eth0');
    }

    /**
     * Test that the named profile is returned
     */
    public function testInfoReturnProfile()
    {
         $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
        ];

        $profilesMetadata = [
            'name' => 'test',
            'description' => 'Some description string',
            'config' => [
                'limits.memory' => '2GB'
            ],
            'devices' => [
                'kvm' => [
                    'path' => '/dev/kvm',
                    'type' => 'unix-char'
                ]
            ]
        ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);
        $profilesReturn                 = $this->generateReturn($this->standardReturn);
        $profilesReturn->body->metadata = $this->generateReturn($profilesMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $profilesReturn);

        $client = new Client($mock);

        $network = $client->profiles->info('eth0');

        $this->assertEquals('test', $network->name);
    }

    /**
     * Test getting all profiles
     * 
     */
    public function testAllProfilesAreReturned()
    {
        $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
        ];

        $profilesMetadata = [
            '/1.0/profiles/default',
            '/1.0/profiles/docker',
        ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);
        $profilesReturn                 = $this->generateReturn($this->standardReturn);
        $profilesReturn->body->metadata = $this->generateReturn($profilesMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $profilesReturn);

        $client = new Client($mock);

        $profiles = $client->profiles->all();

        $this->assertEquals(['default', 'docker'], $profiles);
    }

    /**
     * Test renaming profile
     * 
     */
    public function testRenameProfile()
    {
        $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
        ];

        $profilesMetadata = [
            '/1.0/profiles/default',
            '/1.0/profiles/docker',
        ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);
        $profilesReturn                 = $this->generateReturn($this->standardReturn);
        $profilesReturn->body->metadata = $this->generateReturn($profilesMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $profilesReturn);

        $client = new Client($mock);

        $profiles = $client->profiles->all();

        $this->assertEquals(['default', 'docker'], $profiles);
    }
}
