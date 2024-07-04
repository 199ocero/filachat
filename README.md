# FilaChat

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)


Filachat is a plugin for integrating real-time customer support chat into your application. Provides tools for both customer and agent chat interfaces, with features for managing and maintaining chat conversations.

## Installation

You can install the package via composer:

```bash
composer require jaocero/filachat
```

Now run the following command to setup FilaChat. This handles all the migration, seeding, and config.

```bash
php artisan filachat:install
```

This is the contents of the published config file:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Roles
    |--------------------------------------------------------------------------
    |
    | This option controls whether roles (user, agent) are used in the chat
    | system. If disabled, all users can chat with each other without role
    | constraints.
    |
    */
    'enable_roles' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This option specifies the user model used in the chat system. You can
    | customize this if you have a different user model in your application.
    |
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Agent Model
    |--------------------------------------------------------------------------
    |
    | This option specifies the agent model used in the chat system. You can
    | customize this if you have a different agent model in your application.
    |
    */
    'agent_model' => \App\Models\User::class,
];

```

## Testing

```bash
composer test
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
