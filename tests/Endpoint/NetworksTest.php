<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class NetworksTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Networks';
    }

    /**
     * Test getting all networks
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/networks/eth0',
            '/1.0/networks/lxdbr0',
        ];

        $returnedValue = [
            'eth0',
            'lxdbr0',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/networks/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing network
     */
    public function testShowThatNetworkMatchesRequest()
    {
        $expectedValue = [
            "name" => "lxdbr0",
            "type" => "bridge",
            "used_by" => [
                "/1.0/containers/blah",
            ],
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/networks/lxdbr0')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->show('lxdbr0'));
    }
}
