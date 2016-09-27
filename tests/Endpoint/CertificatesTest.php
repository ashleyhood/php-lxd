<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class CertificatesTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Certificates';
    }

    /**
     * Test getting all certificates
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/certificates/fingerprint1',
            '/1.0/certificates/fingerprint2',
        ];

        $returnedValue = [
            'fingerprint1',
            'fingerprint2',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/certificates/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing certificate
     */
    public function testShowThatCertifcateMatchesRequest()
    {
        $expectedValue = [
            "type" => "client",
            "certificate" => "PEM certificate",
            "name" => "foo",
            "fingerprint" => "SHA256 Hash of the raw certificate"
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/certificates/fingerprint')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->info('fingerprint'));
    }

    /**
     * Test adding certificate
     */
    public function testAddingCertificateWithoutPasswordAndName()
    {
        $expectedValue = [
            "type" => "client",
            "certificate" => "PEM certificate",
            "name" => "foo",
            "password" => "server-trust-password"
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/certificates/')
            ->will($this->returnValue($expectedValue));

        $this->assertStringMatchesFormat('%s', $endpoint->add('PEM certificate'));
    }

    /**
     * Test adding certificate
     */
    public function testAddingCertificateWithPasswordAndName()
    {
        $expectedValue = [
            "type" => "client",
            "certificate" => "PEM certificate",
            "name" => "foo",
            "password" => "server-trust-password"
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/certificates/')
            ->will($this->returnValue($expectedValue));

        $this->assertStringMatchesFormat('%s', $endpoint->add('PEM certificate', 'Super Secret Password', 'hostname'));
    }

    /**
     * Test deleting certificate
     */
    public function testDeletingCertificate()
    {
        $expectedValue = [];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/certificates/fingerprint1')
            ->will($this->returnValue($expectedValue));

        $this->assertEmpty($endpoint->remove('fingerprint1'));
    }
}
