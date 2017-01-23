### LXD server information

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

To get information on the LXD server:

```
<?php

$info = $lxd->host->info();
```

Test if this client is trusted by the LXD server

```
<?php

if ($lxd->host->trusted()) {
    echo 'trusted';
} else {
    echo 'not trusted';
}

```
