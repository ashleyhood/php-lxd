# php-lxd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-ci]][link-ci]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library for interacting with the LXD REST API.

## Install

Via Composer

``` bash
$ composer require opensaucesystems/lxd
```

For usage of this library any httpclient library is needed. If you don't already use one in your project, please install one in advance.

``` bash
$ composer require php-http/guzzle7-adapter
```

## Install for usage with Guzzle 6

``` bash
$ composer require php-http/guzzle6-adapter
$ composer require opensaucesystems/lxd "^0.9"
```

## Usage

See the [`docs`](./docs) for more information.

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

[ico-version]: https://img.shields.io/packagist/v/opensaucesystems/lxd.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ci]: https://github.com/ashleyhood/php-lxd/workflows/Testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/opensaucesystems/lxd.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/opensaucesystems/lxd
[link-ci]: https://github.com/ashleyhood/php-lxd/actions?query=workflow%3ATesting
[link-downloads]: https://packagist.org/packages/opensaucesystems/lxd
[link-author]: https://opensauce.systems
[link-contributors]: ../../contributors
