### Networks

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

Get list of networks:

```
<?php

$networks = $lxd->networks->all();
```

Get network information:

```
<?php

$info = $lxd->networks->info('lxdbr0');
```

> TODO:
> - define a new network
> - replace the network information
> - update the network information
> - rename a network
> - remove a network
