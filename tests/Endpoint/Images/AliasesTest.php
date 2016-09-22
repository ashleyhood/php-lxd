<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint\Images;

use Opensaucesystems\Lxd\Tests\Endpoint\TestCase;

class AliasesTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Images\Aliases';
    }

    /**
     * Test getting all aliases
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/images/aliases/ubuntu/xenial/amd64',
        ];

        $returnedValue = [
            'ubuntu/xenial/amd64',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/images/aliases/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing alias
     */
    public function testShowThatAliasMatchesRequest()
    {
        $expectedValue = [
            "name" => 'ubuntu/xenial/amd64',
            "description" => "x86_64",
            "target" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/images/aliases/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals(
            $expectedValue,
            $endpoint->show('54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473')
        );
    }

    public function testCreateAlias()
    {
        $fingerprint = '54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473';
        $aliasName = 'ubuntu/xenial/amd64';
        $description = 'The alias description';

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/aliases/');

        $this->assertEmpty($endpoint->create($fingerprint, $aliasName, $description));
    }

    public function testReplaceAlias()
    {
        $name = 'ubuntu/xenial/amd64';
        $fingerprint = '54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473';
        $description = 'The alias description';

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('put')
            ->with('/images/aliases/'.$name);

        $this->assertEmpty($endpoint->replace($name, $fingerprint, $description));
    }

    public function testRenameAlias()
    {
        $name = 'ubuntu/xenial/amd64';
        $newName = 'ubuntu-xenial-amd64';

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/aliases/'.$name);

        $this->assertEmpty($endpoint->rename($name, $newName));
    }

    public function testRemoveAlias()
    {
        $name = 'ubuntu/xenial/amd64';

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/images/aliases/'.$name)
            ->will($this->returnValue([]));

        $this->assertEquals([], $endpoint->remove($name));
    }
}
