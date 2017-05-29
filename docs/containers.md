### Containers

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

To get information on containers:

```
<?php

// an array of all container names
$all = $lxd->containers->all();

// info for the container called 'test'
$info = $lxd->containers->info('test'));

// get the current state of the container i.e memory usage etc.
$state = $lxd->containers->state('test');

```

#### Create new containers

From image alias:

```
<?php

$options = ['alias' => 'ubuntu/xenial/amd64'];
$lxd->containers->create('from-alias', $options);
```

From image fingerprint:

```
<?php

$options = ['fingerprint' => 'xxxxxxxxxxxx'];
$lxd->containers->create('from-fingerprint', $options);
```

From image matching properties:

```
<?php

$options = [
    'properties' => [
        'os'           => 'ubuntu',
        'release'      => '14.04',
        'architecture' => 'x86_64',
    ],
];
$lxd->containers->create('from-properties', $options);
```

From private remote server image:

```
<?php

$options = [
    'server' => 'https://private.example.com:8443',
    'alias' => 'ubuntu/xenial/amd64',
    'secret' => 'my_secrect'
];
$lxd->containers->create('remote-private', $options);
```

From public remote server image:

```
<?php

$options = [
    'server' => 'https://images.linuxcontainers.org:8443',
    'alias' => 'ubuntu/xenial/amd64'
];
$lxd->containers->create('remote-public', $options);
```

Create container with empty rootfs:

```
<?php

$options = ['empty' => true];
$lxd->containers->create('empty-rootfs', $options);
```

With addtional container configuration:

```
<?php

$options = [
    'alias'  => 'ubuntu/xenial/amd64',
    'config' => [
        'volatile.eth0.hwaddr' => 'aa:bb:cc:dd:ee:ff',
    ],
    'profiles' => ['default']
];
$lxd->containers->create('with-configuration', $options);
```

Copy a container locally:

```
<?php

$lxd->containers->copy('container-name', 'new-container-name'));
```

Migrate a container to a different LXD server:

```
<?php

$lxd2 = new \Opensaucesystems\Lxd\Client($adapter, '1.0', 'https://lxd2.example.com:8443');
$lxd->containers->migrate($lxd2, 'container-name');

```

> See [lxd/rest.md](https://github.com/lxc/lxd/blob/master/doc/rest-api.md#post-1) for more information on creating containers.

#### Rename container

```
<?php

$lxd->containers->rename('container-name', 'container-rename');
```

#### Remove container

```
<?php

$lxd->containers->remove('container-name');
```

#### Update container

Replace containers configuration.<br />
To avoid lost of configuration first of all current config can be read, changed and then set again.
```
<?php
// Set container ephemeral (delete when stopped)
$container = $lxd->containers->show('test');
$container->ephemeral = true;
$lxd->containers->replace('container-name', $container);
```

Restore a snapshot
```
<?php
$lxd->containers->replace('container-name', ['restore' => 'snapshot-name'] );
```

Update containers configuration.<br />Example: set limit of cpu cores to 4 and rootfs size to 5GB

```
<?php
$newconfig = [
    'config' => [
        'limits.cpu' => 4
    ],
    'devices' => [
        'rootfs' => [
            'size' => '5GB'
        ]
    ]
];
$lxd->containers->update('container-name', $container);
```

#### Change state

Start container:

```
<?php

$lxd->containers->start('container-name');
```

Stop container:

```
<?php

$lxd->containers->stop('container-name');
```

Restart container:

```
<?php

$lxd->containers->restart('container-name');
```

Freeze container:

```
<?php

$lxd->containers->freeze('container-name');
```

Unfreeze container:

```
<?php

$lxd->containers->unfreeze('container-name');
```

#### Execute a command in a container

```
<?php

$lxd->containers->execute('container-name', 'touch /tmp/test.txt');
```

#### Logs

Get all logs:

```
<?php

$lxd->containers->logs->all('container-name');
```

Read log:

```
<?php

$lxd->containers->logs->read('container-name', 'exec_xxxxxxxx.stdout');
```

Remove log:

```
<?php

$lxd->containers->logs->remove('container-name', 'exec_xxxxxxxx.stdout');
```

#### Files

Write to a file:

```
<?php

$lxd->containers->files->write('container-name', '/tmp/test.txt', 'Hello World');
```

Read from a file:

```
<?php

$file = $lxd->containers->files->read('container-name', '/tmp/test.txt');
```

#### Snapshots

View containers snapshots:

```
<?php

$snapshots = $lxd->containers->snapshots->all('container-name');
```

Get snapshot information:

```
<?php

$info = $lxd->containers->snapshots->info('container-name', 'snapshot0');
```

Create snapshot:

```
<?php

$lxd->containers->snapshots->create('container-name', 'snapshot1');
```

Restore snapshot:

```
<?php

$lxd->containers->snapshots->restore('container-name', 'snapshot1');
```

Rename snapshot:

```
<?php

$lxd->containers->snapshots->rename('container-name', 'snapshot1', 'snapshot1-rename');
```

Remove snapshot:

```
<?php

$lxd->containers->snapshots->remove('container-name', 'snapshot0');
```
