# php-lxd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library for interacting with the LXD REST API.

## Install

Via Composer

``` bash
$ composer require opensaucesystems/php-lxd
```

## Usage

``` php
require "vendor/autoload.php";

$uri  = 'https://lxd.example.com:8443';
$cert = 'lxd_client.pem';
$key  = 'lxd_client.key';

$connection = new \Opensaucesystems\Lxd\Connection($uri, $cert, $key, '1.0', false);
$lxd = new \Opensaucesystems\Lxd\Client($connection);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email ashley@opensauce.systems instead of using the issue tracker.

## Credits

- [Ashley Hood][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/opensaucesystems/php-lxd.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/opensaucesystems/php-lxd/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/opensaucesystems/php-lxd.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/opensaucesystems/php-lxd
[link-travis]: https://travis-ci.org/opensaucesystems/php-lxd
[link-downloads]: https://packagist.org/packages/opensaucesystems/php-lxd
[link-author]: https://opensauce.systems
[link-contributors]: ../../contributors
