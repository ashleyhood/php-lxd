<?php

namespace Opensaucesystems\Lxd\Tests\Endpoint;

class ProfilesTest extends TestCase
{
    protected function getEndpointClass()
    {
        return 'Opensaucesystems\Lxd\Endpoint\Profiles';
    }

    /**
     * Test getting all profiles
     */
    public function testAllMatchesRequest()
    {
        $expectedValue = [
            '/1.0/profiles/default',
            '/1.0/profiles/docker',
        ];

        $returnedValue = [
            'default',
            'docker',
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/profiles/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($returnedValue, $endpoint->all());
    }

    /**
     * Test showing profile
     */
    public function testShowThatProfileMatchesRequest()
    {
        $expectedValue = [
            "name" => "test",
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('get')
            ->with('/profiles/test')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->info('test'));
    }

    /**
     * Test creating profile
     */
    public function testCreatingProfileWithOnlyName()
    {
        $expectedValue = [
            "name" => "test",
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/profiles/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create('test'));
    }

    /**
     * Test creating profile
     */
    public function testCreatingProfileWithNameAndDescription()
    {
        $expectedValue = [
            "name" => "test",
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/profiles/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create('test', 'Some description string'));
    }

    /**
     * Test creating profile
     *
     */
    public function testCreatingProfileWithNameAndDescriptionAndConfig()
    {
        $expectedValue = [
            "name" => "test",
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];
        $name = 'test';
        $description = 'Some description string';
        $config = ["limits.memory" => "2GB"];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/profiles/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create($name, $description, $config));
    }

    /**
     * Test creating profile
     *
     */
    public function testCreatingProfile()
    {
        $expectedValue = [
            "name" => "test",
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];
        $name = 'test';
        $description = 'Some description string';
        $config = ["limits.memory" => "2GB"];
        $devices = [
            "kvm" => [
                "type" => "unix-char",
                "path" => "/dev/kvm"
            ],
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/profiles/')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->create($name, $description, $config, $devices));
    }

    /**
     * Test updating profile
     *
     */
    public function testUpdatingProfile()
    {
        $expectedValue = [
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];
        $name = 'test';
        $description = 'Some description string';
        $config = ["limits.memory" => "2GB"];
        $devices = [
            "kvm" => [
                "type" => "unix-char",
                "path" => "/dev/kvm"
            ],
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('patch')
            ->with('/profiles/test')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->update($name, $description, $config, $devices));
    }

    /**
     * Test replacing profile
     *
     */
    public function testReplacingProfile()
    {
        $expectedValue = [
            "description" => "Some description string",
            "config" => [
                "limits.memory" => "2GB",
            ],
            "devices" => [
                "kvm" => [
                    "path" => "/dev/kvm",
                    "type" => "unix-char",
                ]
            ]
        ];
        $name = 'test';
        $description = 'Some description string';
        $config = ["limits.memory" => "2GB"];
        $devices = [
            "kvm" => [
                "path" => "/dev/kvm",
                "type" => "unix-char",
            ],
        ];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('put')
            ->with('/profiles/test')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->replace($name, $description, $config, $devices));
    }

    /**
     * Test renaming profile
     *
     */
    public function testRenamingProfile()
    {
        $name = 'test';
        $newName = 'new-name';
        $expectedValue = ["name" => $newName];

        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('post')
            ->with('/profiles/test')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $endpoint->rename($name, $newName));
    }

    /**
     * Test deleting profile
     */
    public function testDeletingProfile()
    {
        $endpoint = $this->getEndpointMock($this->getEndpointClass());

        $endpoint->expects($this->once())
            ->method('delete')
            ->with('/profiles/test')
            ->will($this->returnValue([]));

        $this->assertEquals([], $endpoint->remove('test'));
    }
}
