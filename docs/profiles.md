### Profiles

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

Get all profiles:

```
<?php

$profiles = $lxd->profiles->all();
```

Get profile information:

```
<?php

$profile = $lxd->profiles->info('plan-one');
```

Create a new profile:

```
<?php

$config = ["limits.memory" => "2GB"];
$devices = [
    "kvm" => [
        "type" => "unix-char",
        "path" => "/dev/kvm"
        ],
    ];

$lxd->profiles->create('profile-name', 'Profile description', $config, $devices);

```

Update profile information:

```
<?php

$config = ["limits.memory" => "4GB"];
$devices = [
    "kvm" => [
        "type" => "unix-char",
        "path" => "/dev/kvm"
        ],
    ];

$lxd->profiles->update('profile-name', 'New profile description', $config, $devices);

```

Replace profile information:

```
<?php

$config = ["limits.memory" => "4GB"];
$devices = [
    "kvm" => [
        "type" => "unix-char",
        "path" => "/dev/kvm"
        ],
    ];

$lxd->profiles->replace('profile-name', 'New profile description', $config, $devices);

```

Rename a profile:

```
<?php

$lxd->profiles->rename('profile-name', 'new-profile-name');
```

Remove a profile:

```
<?php

$lxd->profiles->remove('profile-name');
```
