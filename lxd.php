<?php
use Opensaucesystems\Lxd\Client\Containers;

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
    
    // Save files
    $certFile = __DIR__.'/client.crt';
    file_put_contents($certFile, $certString);
    
    $privkeyFile = __DIR__.'/client.key';
    file_put_contents($privkeyFile, $privkeyString);
}

$uri  = 'https://185.128.58.234:8443';
$cert = 'client.crt';
$key  = 'client.key';

$con = new \Opensaucesystems\Lxd\Connection($uri, $cert, $key, '1.0', false);
// $con = new \Opensaucesystems\Lxd\Connection($uri, '1.0', null, null, false);

// print_r($con->get());

$lxd = new \Opensaucesystems\Lxd\Client($con);

// print_r($lxd->info());

//***********************
// Containers
//***********************
// print_r($lxd->containers->all());
// $container = $lxd->containers->get('test5');
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
// print_r($lxd->containers->info('test5'));

// Rename
// print_r($lxd->containers->rename('test6', 'test6-rename', true));

// Delete
// print_r($lxd->containers->delete('test6-rename', true));

// Change state
// print_r($lxd->containers->info('test4'));
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
// print_r($lxd->containers->info('che-oss-place'));

// Execute command
// print_r($lxd->containers->execute('test4', "echo 'hello world'", [], true));

// Write and read file
// print_r($lxd->containers->state('test4'));
// print_r($lxd->containers->files->write('test4', '/tmp/test.txt', 'Hello World'));
// print_r($lxd->containers->files->read('test4', '/tmp/test.txt'));

// Snapshots
// print_r($lxd->containers('test4')->snapshots->all());

// print_r($lxd->containers->snapshots->all('test4'));
// print_r($lxd->containers->snapshots->info('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot1', false, true));
// print_r($lxd->containers->snapshots->create('test4', 'snapshot1', true, true));
// print_r($lxd->containers->snapshots->rename('test4', 'snapshot1-rename', 'snapshot1', true));
// print_r($lxd->containers->snapshots->delete('test4', 'snapshot1'));
// print_r($lxd->containers->snapshots->delete('test4', 'snapshot1', true));
// print_r($lxd->containers->snapshots->all('test4'));

// print_r($lxd->containers->all());

//***********************
// Images
//***********************
// print_r($lxd->images->all());
// print_r($lxd->images->info('4a63cb23bbf7fc385de738cd5e21816d0aae182dd359ebdecd7d3dc8ded671c1'));

//***********************
// Aliases
//***********************
// print_r($lxd->images->aliases->all());
// print_r($lxd->images->aliases->create('65df07147e458f356db90fa66d6f907a164739b554a40224984317eee729e92a', 'myalias', 'This is a test alias'));
// print_r($lxd->images->aliases->info('myalias'));
// print_r($lxd->images->aliases->rename('ubuntu-1604-amd64', 'ubuntu/xenial/amd64'));
// $lxd->images->aliases->update('ubuntu/xenial/amd64', '4a63cb23bbf7fc385de738cd5e21816d0aae182dd359ebdecd7d3dc8ded671c1', 'Ubuntu 16.04 Xenial Xerus x64');
// print_r($lxd->images->aliases->info('ubuntu/xenial/amd64'));
// print_r($lxd->images->aliases->all());

//***********************
// Certificates
//***********************
// try {
//     print_r($lxd->certificates->all());
// } catch (\Exception $e) {
//     $fingerprint = $lxd->certificates->add(file_get_contents($cert), 'Super secret password');
// }

// print_r($lxd->certificates->all());
// print_r($lxd->certificates->delete('979b6ef21a8e'));
// print_r($lxd->certificates->delete($fingerprint));

// $config = $lxd->info()->config;
// $config->{'images.auto_update_interval'} = '24';
// $config->{'core.trust_password'} = 'Super secret password';

// print_r($lxd->update($config));

//***********************
// Networks
//***********************
// print_r($lxd->networks->all());
// print_r($lxd->networks->info('lxdbr0'));

//***********************
// Profiles
//***********************
// print_r($lxd->profiles->all());
// print_r($lxd->profiles->info('default'));
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
// print_r($lxd->profiles->delete('external'));
// print_r($lxd->profiles->all());

//***********************
// Operations
//***********************
// print_r($lxd->operations->all());
