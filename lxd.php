<?php

/**
 * Setup a LXD for remote access:
 * 
 *  lxc config set core.https_address [::]:8443
 *  lxc config set core.trust_password 'Super secret password'
 * 
 */

require "vendor/autoload.php";

// Create certificate
if (!file_exists(__DIR__.'/client.key')) {
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
    
    // Generate files
    openssl_x509_export($cert, $certString);
    openssl_pkey_export($privkey, $privkeyString);

    $pemString = $certString.$privkeyString;

    // Save files
    $certFile = __DIR__.'/client.crt';
    file_put_contents($certFile, $certString);
    
    $privkeyFile = __DIR__.'/client.key';
    file_put_contents($privkeyFile, $privkeyString);
    
    $pemFile = __DIR__.'/client.pem';
    file_put_contents($pemFile, $pemString);
}

$url  = 'https://127.0.0.1:4443';
$cert = 'client.crt';
$key  = 'client.key';
$pem  = __DIR__.'/client.pem';

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$config = [
    'verify' => false,
    'cert' => [
        $pem,
        ''
    ]
];

$guzzle = new GuzzleClient($config);
$adapter = new GuzzleAdapter($guzzle);

$lxd = new \Opensaucesystems\Lxd\Client($adapter);

$lxd->setUrl($url);

print_r($lxd->host->show());
// var_dump($lxd->host->trusted());

//***********************
// Containers
//***********************
// print_r($lxd->containers->all());
// print_r($lxd->containers->show('test4'));
// print_r($lxd->containers->state('test4'));
// $container = $lxd->containers->show('test4');
// print_r($container);
// print_r($lxd->containers->state('test4'));

// Create
// $options = ["alias" => "ubuntu/xenial/amd64",];
// $options = ["fingerprint" => "097e75d6f7419d3a5e204d8125582f2d7bdd4ee4c35bd324513321c645f0c415",];
// $options = [
//     "properties" => [
//         "os"           => "ubuntu",
//         "release"      => "14.04",
//         "architecture" => "x86_64",
//     ],
// ];
// $options = ["empty" => true,];
// $options = [
//     "alias"  => "ubuntu/xenial/amd64",
//     "config" => [
//         "volatile.eth0.hwaddr" => "aa:bb:cc:dd:ee:ff",
//     ],
// ];
// $options = [
//     "alias"  => "ubuntu/xenial/amd64",
//     "profiles" => ["migratable", "unconfined"],
// ];
// $options = [
//     "server" => "https://images.linuxcontainers.org:8443",
//     "alias"  => "ubuntu/xenial/amd64",
// ];
// $options = [
//     "server" => "https://private.example.com:8443",
//     "alias" => "ubuntu/xenial/amd64",
//     "secret" => "my_secrect",
// ];
// $options = [
//     "alias" => "ubuntu/xenial/amd64",
//     "secrect" => "my_secrect",
// ];
// $options = [
//     "server" => "https://images.linuxcontainers.org:8443",
//     "alias"  => "ubuntu/xenial/amd64",
//     "protocol" => "lxd",
// ];
// print_r($lxd->containers->create('che-oss-place', $options, true));
// print_r($lxd->containers->create('test7', [], true));
// print_r($lxd->containers->copy('test6-copy', 'test6', [], true));
// print_r($lxd->containers->all());

// Update
// $container = $lxd->containers->get('test5');
// print_r($container);
// $container->ephemeral = true;
// print_r($lxd->containers->update('test5', $container, true));
// print_r($lxd->containers->show('test5'));

// Rename
// print_r($lxd->containers->rename('test6', 'test6-rename', true));

// Delete
// print_r($lxd->containers->remove('test6-rename', true));

// Change state
// print_r($lxd->containers->show('test4'));
// print_r($lxd->containers->start('che-oss-place'));
// print_r($lxd->containers->start('test4', 30, false, false, true));
// print_r($lxd->containers->restart('test4'));
// print_r($lxd->containers->restart('test4', 30, true, false, true));
// print_r($lxd->containers->stop('che-oss-place'));
// print_r($lxd->containers->stop('test4', 30, false, false, true));
// print_r($lxd->containers->freeze('test4'));
// print_r($lxd->containers->freeze('test4', 30, true, false, true));
// print_r($lxd->containers->unfreeze('test4'));
// print_r($lxd->containers->unfreeze('test4', 30, true, false, true));
// print_r($lxd->containers->show('che-oss-place'));

// Execute command
// print_r($lxd->containers->execute('test4', "echo 'hello world'", [], true));

// Write and read file
// print_r($lxd->containers->state('test4'));
// print_r($lxd->containers->files->write('test4', '/tmp/test.txt', 'Hello World'));
// print_r($lxd->containers->files->read('test4', '/tmp/test.txt'));

// Snapshots
// print_r($lxd->containers->snapshots->all('test4'));
// print_r($lxd->containers->snapshots->show('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot2', false, true));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot3', true, true));
// print_r($lxd->containers->snapshots->rename('test4', 'snapshot1', 'snapshot1-rename', true));
// print_r($lxd->containers->snapshots->remove('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->remove('test4', 'snapshot1', true));
// print_r($lxd->containers->snapshots->all('test4'));

// print_r($lxd->containers->all());

//***********************
// Images
//***********************
// print_r($lxd->images->all());
// print_r($lxd->images->show('c97972fc6529e9cb8831c52fa623b776b176665871bf5f30f39d6da2f2d23900'));
// print_r($lxd->images->createFromRemote(
//     "https://images.linuxcontainers.org:8443",
//     [
//         "alias"  => "ubuntu/xenial/amd64",
//     ],
//     true,
//     true
// ));
// print_r($lxd->images->remove('c97972fc6529e9cb8831c52fa623b776b176665871bf5f30f39d6da2f2d23900', true));
// print_r($lxd->images->all());

//***********************
// Aliases
//***********************
// print_r($lxd->images->aliases->all());
// print_r($lxd->images->aliases->create('c97972fc6529e9cb8831c52fa623b776b176665871bf5f30f39d6da2f2d23900', 'ubuntu/xenial/amd64', 'This is a test alias'));
// print_r($lxd->images->aliases->replace('ubuntu/xenial/amd64', 'c97972fc6529e9cb8831c52fa623b776b176665871bf5f30f39d6da2f2d23900', 'New description'));
// print_r($lxd->images->aliases->rename('ubuntu/xenial/amd64', 'ubuntu-1604-amd64'));
// print_r($lxd->images->aliases->rename('ubuntu-1604-amd64', 'ubuntu/xenial/amd64'));
// $lxd->images->aliases->replace('ubuntu/xenial/amd64', 'c97972fc6529e9cb8831c52fa623b776b176665871bf5f30f39d6da2f2d23900', 'Ubuntu 16.04 Xenial Xerus x64');
// print_r($lxd->images->aliases->remove('ubuntu/xenial/amd64'));
// print_r($lxd->images->aliases->show('ubuntu/xenial/amd64'));
// print_r($lxd->images->aliases->show('ubuntu-1604-amd64'));
// print_r($lxd->images->aliases->all());

//***********************
// Certificates
//***********************
// try {
//     print_r($lxd->certificates->all());
// } catch (\Exception $e) {
//     $fingerprint = $lxd->certificates->add(file_get_contents($cert), 'Super secret password', 'test-host');
//     var_dump($fingerprint);
// }

// print_r($lxd->certificates->show($fingerprint));
// $lxd->certificates->remove('2a6c24f521c2ceffe658c9fc729023ff48bc050d37ad4f1d8a1bb7709a5ecf7b');
// $lxd->certificates->remove($fingerprint);

// $config = $lxd->show()->config;
// $config->{'images.auto_update_interval'} = '24';
// $config->{'core.trust_password'} = 'Super secret password';

// print_r($lxd->update($config));

//***********************
// Networks
//***********************
// print_r($lxd->networks->all());
// print_r($lxd->networks->show('lxdbr0'));

//***********************
// Profiles
//***********************
// print_r($lxd->profiles->all());
// $profile = $lxd->profiles->show('plan-one');
// $profile->description = 'Containers have 1GB of RAM, 1 CPU core';
// $profile->config->{'limits.cpu'} = 1;
// print_r($profile);
// print_r($lxd->profiles->remove('external'));
// $description = 'Ip address is on the same network as host';
// $config = null;
// $devices = [
//     'eth0' => [
//         'nictype' => 'macvlan',
//         'parent'  => 'ens18',
//         'type'    => 'nic'
//     ]
// ];
// print_r($lxd->profiles->create('external', $description, $config, $devices));
// print_r($lxd->profiles->remove('external'));
// print_r($lxd->profiles->all());

//***********************
// Operations
//***********************
// print_r($lxd->operations->all());
// $uuid = $lxd->operations->all();
// print_r($lxd->operations->show($uuid[0][0]));
