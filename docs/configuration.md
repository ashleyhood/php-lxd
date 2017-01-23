### Setup a LXD for remote access

On the LXD server you will need to allow access over the network by setting the following configs:

     lxc config set core.https_address [::]:8443
     lxc config set core.trust_password 'Super secret password'

### Connect to LXD server

To connect to the LXD server you will need a certificate for the client.

Here is how to create one in PHP:

```
<?php

// Create certificate
$dn = array(
    "countryName"            => "UK",
    "stateOrProvinceName"    => "Isle Of Wight",
    "localityName"           => "Cowes",
    "organizationName"       => "Open Sauce Systems",
    "organizationalUnitName" => "Dev",
    "commonName"             => "127.0.0.1",
    "emailAddress"           => "info@opensauce.systems"
);

// Generate certificate
$privkey = openssl_pkey_new();
$cert    = openssl_csr_new($dn, $privkey);
$cert    = openssl_csr_sign($cert, null, $privkey, 365);

// Generate strings
openssl_x509_export($cert, $certString);
openssl_pkey_export($privkey, $privkeyString);

// Save to file
$pemFile = __DIR__.'/client.pem';
file_put_contents($pemFile, $certString.$privkeyString);

```

Once you have a ssl certificate, you can use this to connect to the LXD server:

```
require "vendor/autoload.php";

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$config = [
    'verify' => false,
    'cert' => [
        __DIR__.'/client.pem',
        ''
    ]
];

$guzzle = new GuzzleClient($config);
$adapter = new GuzzleAdapter($guzzle);

$lxd = new \Opensaucesystems\Lxd\Client($adapter);

$lxd->setUrl('https://lxd.example.com:8443');

```
