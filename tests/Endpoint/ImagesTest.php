<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class ImagesTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Images';
    }

    /**
     * Test getting all images
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/images/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473',
        ];

        $returnedValue = [
            '54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing image
     */
    public function testShowThatImageMatchesRequest()
    {
        $expectedValue = [
            "aliases" => [
                [
                    "name" => "trusty",
                    "description" => "",
                ]
            ],
            "architecture" => "x86_64",
            "auto_update" => true,
            "cached" => false,
            "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
            "filename" => "ubuntu-trusty-14.04-amd64-server-20160201.tar.xz",
            "properties" => [
                "architecture" => "x86_64",
                "description" => "Ubuntu 14.04 LTS server (20160201)",
                "os" => "ubuntu",
                "release" => "trusty"
            ],
            "update_source" => [
                "server" => "https://10.1.2.4:8443",
                "protocol" => "lxd",
                "certificate" => "PEM certificate",
                "alias" => "ubuntu/trusty/amd64"
            ],
            "public" => false,
            "size" => 123792592,
            "created_at" => "2016-02-01T21:07:41Z",
            "expires_at" => "1970-01-01T00:00:00Z",
            "last_used_at" => "1970-01-01T00:00:00Z",
            "uploaded_at" => "2016-02-16T00:44:47Z"
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/images/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals(
            $expectedValue,
            $endpoint->info('54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473')
        );
    }

    /**
     * Test showing image
     */
    public function testShowWithSecret()
    {
        $expectedValue = [
            "aliases" => [
                [
                    "name" => "trusty",
                    "description" => "",
                ]
            ],
            "architecture" => "x86_64",
            "auto_update" => true,
            "cached" => false,
            "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
            "filename" => "ubuntu-trusty-14.04-amd64-server-20160201.tar.xz",
            "properties" => [
                "architecture" => "x86_64",
                "description" => "Ubuntu 14.04 LTS server (20160201)",
                "os" => "ubuntu",
                "release" => "trusty"
            ],
            "update_source" => [
                "server" => "https://10.1.2.4:8443",
                "protocol" => "lxd",
                "certificate" => "PEM certificate",
                "alias" => "ubuntu/trusty/amd64"
            ],
            "public" => false,
            "size" => 123792592,
            "created_at" => "2016-02-01T21:07:41Z",
            "expires_at" => "1970-01-01T00:00:00Z",
            "last_used_at" => "1970-01-01T00:00:00Z",
            "uploaded_at" => "2016-02-16T00:44:47Z"
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/images/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473?secret=SECRET')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals(
            $expectedValue,
            $endpoint->info('54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473', 'SECRET')
        );
    }

    /**
     * Test creating
     */
    public function testCreateWithWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $options =
            [
                "server" => "https://images.linuxcontainers.org:8443",
                "alias"  => "ubuntu/xenial/amd64",
            ];
        $headers = [];
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create($options, $headers, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateWithWaitTrue()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Success",
            "status_code" => 200,
            "resources" => "",
            "metadata" => [
                "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                "size" => 84920722,
            ],
            "may_cancel" => "",
            "err" => "",
        ];
        $options =
            [
                "server" => "https://images.linuxcontainers.org:8443",
                "alias"  => "ubuntu/xenial/amd64",
            ];
        $headers = [];
        $wait = true;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create($options, $headers, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateFromRemoteWithAutoUpdateFalseAndWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
                "alias"  => "ubuntu/xenial/amd64",
            ];
        $autoUpdate = false;
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromRemote($server, $options, $autoUpdate, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateFromRemoteWithAutoUpdateTrueAndWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
                "alias"  => "ubuntu/xenial/amd64",
            ];
        $autoUpdate = true;
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromRemote($server, $options, $autoUpdate, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateFromRemoteWithAutoUpdateTrueAndWaitTrue()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Success",
            "status_code" => 200,
            "resources" => "",
            "metadata" => [
                "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                "size" => 84920722,
            ],
            "may_cancel" => "",
            "err" => "",
        ];
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
                "alias"  => "ubuntu/xenial/amd64",
            ];
        $autoUpdate = true;
        $wait = true;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromRemote($server, $options, $autoUpdate, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateFromRemoteWithFingerprintAndProtocolAndSecretAndCertificate()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Success",
            "status_code" => 200,
            "resources" => "",
            "metadata" => [
                "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                "size" => 84920722,
            ],
            "may_cancel" => "",
            "err" => "",
        ];
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
                "filename" => "filename",
                "public" => true,
                "properties" => [
                    "os" => "Ubuntu"
                ],
                "protocol" => "lxd",
                "secret" => "my-secret-string",
                "certificate" => "PEM certificate",
                "fingerprint"  => "ubuntu/xenial/amd64",
            ];
        $autoUpdate = true;
        $wait = true;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromRemote($server, $options, $autoUpdate, $wait));
    }

    public function testCreateFromRemoteWithIncorrectProtocol()
    {
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
                "protocol" => "http",
                "secret" => "my-secret-string",
                "certificate" => "PEM certificate",
                "fingerprint"  => "ubuntu/xenial/amd64",
            ];
        $autoUpdate = false;
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid protocol.  Valid choices: lxd, simplestreams');

        $endpoint->createFromRemote($server, $options, $autoUpdate, $wait);
    }

    public function testCreateFromRemoteWithNoAliasOrFingerprint()
    {
        $server = "https://images.linuxcontainers.org:8443";
        $options =
            [
            ];
        $autoUpdate = true;
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Alias or Fingerprint must be set');

        $endpoint->createFromRemote($server, $options, $autoUpdate, $wait);
    }

    /**
     * Test creating
     */
    public function testCreateFromContainerWithWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $name = "container_name";
        $options =
            [
                "filename" => "filename",
                "public" => true,
                "properties" => [
                    "os" => "Ubuntu"
                ],
            ];
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromContainer($name, $options, $wait));
    }

    /**
     * Test creating
     */
    public function testCreateFromSnapshotWithWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $name = "container_name";
        $snapshot = "snapshot_name";
        $options =
            [
                "filename" => "filename",
                "public" => true,
                "properties" => [
                    "os" => "Ubuntu"
                ],
            ];
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/images/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->createFromSnapshot($name, $snapshot, $options, $wait));
    }

    public function testReplaceImageOptionsWithWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $fingerprint = "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473";
        $options =
            [
                "auto_update" => true,
                "public" => true,
                "properties" => [
                    "os" => "Ubuntu"
                ],
            ];
        $wait = false;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('put')
            ->with('/images/'.$fingerprint)
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->replace($fingerprint, $options, $wait));
    }

    public function testReplaceImageOptionsWithWaitTrue()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Success",
            "status_code" => 200,
            "resources" => "",
            "metadata" => [
                "fingerprint" => "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                "size" => 84920722,
            ],
            "may_cancel" => "",
            "err" => "",
        ];
        $fingerprint = "54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473";
        $options =
            [
                "auto_update" => true,
                "public" => true,
                "properties" => [
                    "os" => "Ubuntu"
                ],
            ];
        $wait = true;

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('put')
            ->with('/images/'.$fingerprint)
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->replace($fingerprint, $options, $wait));
    }

    public function testRemoveImageWithWaitFalse()
    {
        $expectedValue = [
            "id" => "8497e426-984b-4390-8f7c-430b6a317acc",
            "class" => "task",
            "created_at" => "2016-09-22T11:05:56.382272279+01:00",
            "updated_at" => "2016-09-22T11:05:56.382272279+01:00",
            "status" => "Running",
            "status_code" => 103,
            "resources" => "",
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $wait = false;
        $fingerprint = '54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473';

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/images/'.$fingerprint)
            ->will($this->returnValue([]));

        $this->assertEquals([], $endpoint->remove($fingerprint));
    }

    public function testRemoveImageWithWaitTrue()
    {
        $expectedValue = [
            "id" => "cd7882b1-bc1c-4075-b3f6-4cde2e5d0768",
            "class" => "task",
            "created_at" => "2016-09-22T11:01:51.994116768+01:00",
            "updated_at" => "2016-09-22T11:01:51.994116768+01:00",
            "status" => "Success",
            "status_code" => 200,
            "resources" => [
                    "images" => [
                            "/1.0/images/54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473",
                    ],
                ],
            "metadata" => "",
            "may_cancel" => "",
            "err" => "",
        ];
        $wait = true;
        $fingerprint = '54c8caac1f61901ed86c68f24af5f5d3672bdc62c71d04f06df3a59e95684473';

        $endpoint = $this->getEndpointMock($this->getEndpointClass(), $wait, $expectedValue);

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/images/'.$fingerprint)
            ->will($this->returnValue([]));

        $this->assertEquals([], $endpoint->remove($fingerprint));
    }

    public function testGetEndpointDoesNotExist()
    {
        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $this->expectException(\Opensaucesystems\Lxd\Exception\InvalidEndpointException::class);

        $endpoint->__get('invalidendpoint');
    }
}
