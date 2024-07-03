# FilaChat is a plugin for integrating real-time customer support chat into your application. Provides tools for both customer and agent chat interfaces, with features for managing and maintaining chat conversations.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require jaocero/filachat
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filachat-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filachat-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filachat-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Testing

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jay-Are Ocero](https://github.com/199ocero)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
