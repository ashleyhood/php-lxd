<?php

namespace Opensaucesystems\Lxd;

use Mockery;

class NetworksTest extends \PHPUnit_Framework_TestCase
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
     * Test getting newtork by name when client is untrusted
     * 
     * @expectedException \Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed
     */
    public function testInfoOfNetworkWhenUntrusted()
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

        $network = $client->networks->info('eth0');
    }

    /**
     * Test that the named network is returned
     */
    public function testInfoReturnNetworkDetails()
    {
         $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
        ];

        $networksMetadata = [
            "name"    => "lxdbr0",
            "type"    => "bridge",
            "used_by" => ["/1.0/containers/blah"],
            ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);
        $networksReturn                 = $this->generateReturn($this->standardReturn);
        $networksReturn->body->metadata = $this->generateReturn($networksMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $networksReturn);

        $client = new Client($mock);

        $network = $client->networks->info('eth0');

        $this->assertEquals('lxdbr0', $network->name);
        $this->assertEquals('bridge', $network->type);
        $this->assertEquals(["/1.0/containers/blah"], $network->used_by);
    }

    /**
     * Test getting all networks
     * 
     */
    public function testAllNetworksAreReturned()
    {
        $trustedMetadata = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'trusted',
            'public'         => false,
        ];

        $networksMetadata = [
            "/1.0/networks/eth0",
            "/1.0/networks/lxdbr0",
        ];

        $trustedReturn                  = $this->generateReturn($this->standardReturn);
        $trustedReturn->body->metadata  = $this->generateReturn($trustedMetadata);
        $networksReturn                 = $this->generateReturn($this->standardReturn);
        $networksReturn->body->metadata = $this->generateReturn($networksMetadata);

        $mock = Mockery::mock('Opensaucesystems\Lxd\Connection');
        $mock->shouldReceive('get')->twice()->andReturn($trustedReturn, $networksReturn);

        $client = new Client($mock);

        $networks = $client->networks->all();

        $this->assertEquals(['eth0', 'lxdbr0'], $networks);
    }
}
