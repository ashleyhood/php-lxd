### Operations

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

Get all operations:

```
<?php

$operations = $lxd->operations->all();
```

Get operation information:

```
<?php

$info = $lxd->operations->info($uuid);
```

Cancel an operation:

```
<?php

$lxd->operations->cancel($uuid);
```

Wait for an operation to finish:

```
<?php

$lxd->operation->wait($uuid, $timeout);
```
