<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    abstract protected function getEndpointClass();

    protected function getEndpointMock($endpointClass, $wait = false, $expectedValue = null)
    {
        $httpClient = $this->getMock('Http\Client\HttpClient', ['sendRequest']);

        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        if ($wait) {
            $client = $this->getMockBuilder('\Opensaucesystems\Lxd\Client')
            ->setMethods(['__get'])
            ->setConstructorArgs([$httpClient])
            ->getMock();
            
            $operations = $this->getMockBuilder('Opensaucesystems\Lxd\Endpoint\Operations')
                ->setMethods(['wait'])
                ->setConstructorArgs([$client])
                ->getMock();

            $operations->expects($this->any())
                ->method('wait')
                ->with($expectedValue['id'])
                ->will($this->returnValue($expectedValue));

            $client->expects($this->any())
                ->method('__get')
                ->with($this->equalTo('operations'))
                ->will($this->returnValue($operations));
        } else {
            $client = new \Opensaucesystems\Lxd\Client($httpClient);
        }

        return $this->getMockBuilder($endpointClass)
            ->setMethods(['get', 'post', 'patch', 'delete', 'put'])
            ->setConstructorArgs([$client])
            ->getMock();
    }
}
