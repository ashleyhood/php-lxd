<?php

namespace Opensaucesystems\Lxd;

use Mockery;

class HostTest extends \PHPUnit_Framework_TestCase
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
     */
    public function testInstanceOf()
    {
        $client = new Client();

        $this->assertInstanceOf('Opensaucesystems\Lxd\Client\Host', $client->host);
    }

    /**
     * Test untrusted connection to get info
     */
    public function testInfo()
    {
        $httpClient = $this->getMock('Http\Client\HttpClient', array('sendRequest'));
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = new Client($httpClient);

        $endpoint = $this->getMockBuilder('Opensaucesystems\Lxd\Client\Host')
            ->setMethods(array('get', 'post', 'patch', 'delete', 'put'))
            ->setConstructorArgs(array($client))
            ->getMock();

        $expectedArray = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $endpoint->info());
    }

    /**
     * Test client is not trusted
     */
    public function testTrustedIsTrue()
    {
        $httpClient = $this->getMock('Http\Client\HttpClient', array('sendRequest'));
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = new Client($httpClient);

        $endpoint = $this->getMockBuilder('Opensaucesystems\Lxd\Client\Host')
            ->setMethods(array('get', 'post', 'patch', 'delete', 'put'))
            ->setConstructorArgs(array($client))
            ->getMock();

        $expectedArray = [
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

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedArray));

        $this->assertTrue($endpoint->trusted());
    }

    /**
     * Test untrusted server config replace
     *
     * @expectedException \Opensaucesystems\Lxd\Exception\ClientAuthenticationFailed
     */
    public function testUpdateUntrustedThrowsException()
    {
        $httpClient = $this->getMock('Http\Client\HttpClient', array('sendRequest'));
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = new Client($httpClient);

        $endpoint = $this->getMockBuilder('Opensaucesystems\Lxd\Client\Host')
            ->setMethods(array('get', 'post', 'patch', 'delete', 'put'))
            ->setConstructorArgs(array($client))
            ->getMock();

        $expectedArray = [
            'api_extensions' => [],
            'api_status'     => 'stable',
            'api_version'    => '1.0',
            'auth'           => 'untrusted',
            'public'         => false,
        ];

        $endpoint->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($expectedArray));

        $config                                = [];
        $config['images.auto_update_interval'] = '24';
        $config['core.trust_password']         = 'my-new-password';

        $endpoint->replace($config);
    }

    /**
     * Test trusted server config update
     */
    public function testUpdateSetAutoUpdateInterval()
    {
        $httpClient = $this->getMock('Http\Client\HttpClient', array('sendRequest'));
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = new Client($httpClient);

        $endpoint = $this->getMockBuilder('Opensaucesystems\Lxd\Client\Host')
            ->setMethods(array('get', 'post', 'patch', 'delete', 'put'))
            ->setConstructorArgs(array($client))
            ->getMock();

        $expectedArray = [
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

        $updatedArray = $expectedArray;
        $updatedArray['config']['images.auto_update_interval'] = '24';

        $endpoint->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($expectedArray));
        $endpoint->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($expectedArray));
        $endpoint->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue($updatedArray));

        $endpoint->expects($this->once())
            ->method('put')
            ->with('', ['config'=>$updatedArray['config']])
            ->will($this->returnValue($updatedArray));

        $info = $endpoint->info();

        $info['config']['images.auto_update_interval'] = '24';

        $update = $endpoint->replace($info['config']);

        $this->assertEquals('24', $update['config']['images.auto_update_interval']);
    }

    /**
     * Test server config update failed
     *
     * @expectedException \Http\Client\Common\Exception\ClientErrorException
     */
    public function testUpdateConfigNotSavedThrowsException()
    {
        $error = [
            "type" => "error",
            "error" => "Failure",
            "error_code" => 400,
            "metadata" => []
        ];

        $httpClient = new  \Http\Mock\Client();
        $responseError = new \GuzzleHttp\Psr7\Response(400, [], json_encode($error));
        $httpClient->addResponse($responseError);

        $client = new Client($httpClient);

        $client->host->info();
    }
}
