### Images

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

Get all images:

```
<?php

$images = $lxd->images->all();

```

Get image information:

```
<?php

$info = $lxd->images->info('xxxxxxxx'));
```

Create image from remote LXD image server:

```
<?php

$lxd->images->createFromRemote(
    'https://images.linuxcontainers.org:8443',
    [
        'alias'  => 'ubuntu/xenial/amd64',
    ]
);
```

Create image from snapshot:

```
<?php

$lxd->images->createFromSnapshot('container-name', 'snap0');
```

Remove image:

```
<?php

$lxd->images->remove('xxxxxxxx');
```

### Aliases

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

Get all aliases:

```
<?php

$aliases = $lxd->images->aliases->all();
```

Get alias description and target

```
<?php

$lxd->images->aliases->info('ubuntu/xenial/amd64');
```

Create an alias:

```
<?php

$lxd->images->aliases->create('xxxxxxxx', 'alias-name', 'Alias description');
```

Replaces the alias target or description:

```
<?php

$lxd->images->aliases->replace('alias-name', 'xxxxxxxx', 'New description');
```

Rename an alias:

```
<?php

$lxd->images->aliases->rename('alias-name', 'new-alias-name');
```

Remove alias:

```
<?php

$lxd->images->aliases->remove('ubuntu/xenial/amd64');
```
