### Certificates

> NOTE: If you haven't setup your LXD server, read [configuration.md](configuration.md)

List of trusted certificates:

```
<?php

$certificates = $lxd->certificates->all();
```

Add a new trusted certificate:

```
<?php

$fingerprint = $lxd->certificates->add(file_get_contents(__DIR__.'/client.pem'), 'Super secret password');
```

Get trusted certificate information:

```
<?php

$info = $lxd->certificates->info($fingerprint);
```

Remove trusted certificate:

```
<?php

$lxd->certificates->remove('xxxxxxxx');
```
