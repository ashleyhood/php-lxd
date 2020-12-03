<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

use PHPUnit\Framework\TestCase as FrameworkTestCase;

abstract class TestCase extends FrameworkTestCase
{
    abstract protected function getEndpointClass();

    protected function getEndpointMock($endpointClass, $wait = false, $expectedValue = null)
    {
        $httpClient = $this->getMockBuilder('Http\Client\HttpClient')->onlyMethods(['sendRequest'])->getMock();

        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        if ($wait) {
            $client = $this->getMockBuilder('\Opensaucesystems\Lxd\Client')
            ->onlyMethods(['__get'])
            ->setConstructorArgs([$httpClient])
            ->getMock();

            $operations = $this->getMockBuilder('Opensaucesystems\Lxd\Endpoint\Operations')
                ->onlyMethods(['wait'])
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
            ->onlyMethods(['get', 'post', 'patch', 'delete', 'put'])
            ->setConstructorArgs([$client])
            ->getMock();
    }
}
