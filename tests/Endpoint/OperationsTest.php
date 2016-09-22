<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class OperationsTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Operations';
    }

    /**
     * Test getting all operations
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/operations/c0fc0d0d-a997-462b-842b-f8bd0df82507',
            '/1.0/operations/092a8755-fd90-4ce4-bf91-9f87d03fd5bc',
        ];

        $returnedValue = [
            'c0fc0d0d-a997-462b-842b-f8bd0df82507',
            '092a8755-fd90-4ce4-bf91-9f87d03fd5bc',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/operations/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing operation
     */
    public function testShowThatOperationMatchesRequest()
    {
        $expectedValue = [
            "id" => "c0fc0d0d-a997-462b-842b-f8bd0df82507",
            "class" => "token",
            "created_at" => "2016-02-17T16:59:27.237628195-05:00",
            "updated_at" => "2016-02-17T16:59:27.237628195-05:00",
            "status" => "Running",
            "status_code" => "103",
            "resources" => [
                "images" => [
                    "/1.0/images/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                ],
            ],
            "metadata" => [
                "secret" => "c9209bee6df99315be1660dd215acde4aec89b8e5336039712fc11008d918b0d",
            ],
            "may_cancel" => true,
            "err" => "",
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/operations/c0fc0d0d-a997-462b-842b-f8bd0df82507')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->show('c0fc0d0d-a997-462b-842b-f8bd0df82507'));
    }

    /**
     * Test cancelling operation
     */
    public function testCancellingOperation()
    {
        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/operations/c0fc0d0d-a997-462b-842b-f8bd0df82507')
            ->will($this->returnValue([]));

        $this->assertEquals([], $endpoint->cancel('c0fc0d0d-a997-462b-842b-f8bd0df82507'));
    }

    /**
     * Test wait operation
     */
    public function testWaitingOperationWithoutTimeout()
    {
        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/operations/c0fc0d0d-a997-462b-842b-f8bd0df82507/wait');

        $this->assertEmpty($endpoint->wait('c0fc0d0d-a997-462b-842b-f8bd0df82507'));
    }

    /**
     * Test wait operation with timeout
     */
    public function testWaitingOperationWithTimeout()
    {
        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/operations/c0fc0d0d-a997-462b-842b-f8bd0df82507/wait?timeout=30');

        $this->assertEmpty($endpoint->wait('c0fc0d0d-a997-462b-842b-f8bd0df82507', 30));
    }
}
