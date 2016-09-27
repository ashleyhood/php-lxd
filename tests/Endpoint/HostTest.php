<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class HostTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Host';
    }

    /**
     * Test untrusted connection to get info
     */
    public function testInfoMatchesUntrustedRequest()
    {
        $expectedValue = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->info());
    }

    /**
     * Test client is not trusted
     */
    public function testInfoMatchesTrustedRequest()
    {
        $expectedValue = [
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

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->info());
    }

    /**
     * Test trusted
     */
    public function testTrustedIsFalse()
    {
        $expectedValue = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedValue));

        $this->assertFalse($endpoint->trusted());
    }

    /**
     * Test trusted
     */
    public function testTrustedIsTrue()
    {
        $expectedValue = [
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

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedValue));

        $this->assertTrue($endpoint->trusted());
    }

    /**
     * Test server config update
     */
    public function testUpdateCanSetAutoUpdateInterval()
    {
        $expectedValue = [
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
        $data = ['config' => ['images.auto_update_interval' => '24']];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('put')
            ->with('', $data)
            ->will($this->returnValue([]));
        $endpoint->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->replace($data['config']));
    }
}
