# FilaChat

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jaocero/filachat/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jaocero/filachat/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/filachat.svg?style=flat-square)](https://packagist.org/packages/jaocero/filachat)


Filachat is a plugin for adding real-time customer support chat to your application. It provides tools for both customer and agent chat interfaces, with features for managing and maintaining conversations. You can also disable role constraints to let users chat with each other without restrictions.

> [!IMPORTANT]  
> This plugin has two roles: `agent` and `user`. When role restrictions are enabled, `agents` cannot chat with each other, and `users` cannot chat with each other. Only `agents` and `users` can chat with each other, and vice versa. If role restrictions are disabled, `agents` and `users` can freely chat with one another without any restrictions.

> [!CAUTION]
> This plugin has not been tested in a production environment. Use it at your own risk.

## Installation

You can install the package via composer:

```bash
composer require jaocero/filachat
```

Run the following command to install FilaChat, which will take care of all migrations and configurations.

```bash
php artisan filachat:install
```

This is the contents of the published config file:

```php
<?php

return [
    'enable_roles' => true,
    'user_model' => \App\Models\User::class,
    'agent_model' => \App\Models\User::class,
    'sender_name_column' => 'name',
    'receiver_name_column' => 'name',
    'slug' => 'filachat',
    'navigation_icon' => 'heroicon-o-chat-bubble-bottom-center',
    'max_content_width' => \Filament\Support\Enums\MaxWidth::Full,
];

```

> [!NOTE]  
> This step is optional if you want to enable role restrictions. You only need to create an agent if you want to set up role-based chat support.

When you first install this plugin, you won’t have any `agents` set up yet. Agents are like admins who can provide chat support to your customers or users. 

To create an `agent`, use the command below:

```bash
php artisan filachat:agent-create
```

Next, you need to apply the `HasFilaChat` trait to your models, whether it’s the `agent` model or the `user` model.

```php
<?php

//...
use JaOcero\FilaChat\Traits\HasFilaChat;
//...
class User extends Authenticatable
{
    //...
    use HasFilaChat;
    //...
}
```

To integrate this plugin into your `FilamentPHP` application, you need to apply a custom theme.

> **Custom Theme Installation**
> [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme)

Then add the plugin's views to your `tailwind.config.js` file.

```js
content: [
    ...
    './vendor/jaocero/filachat/resources/views/**/**/*.blade.php',
    ...
]
```

## Usage
You can now use this plugin and add it to your FilamentPHP panel provider.
```php
<?php
//...
use JaOcero\FilaChat\FilaChatPlugin;
//..
class AdminPanelProvider extends PanelProvider
{
    //...
    public function panel(Panel $panel): Panel
    {
        return $panel
            //...
            ->plugins([
                FilaChatPlugin::make()
            ]);
    }

    //...
}
```

> [!IMPORTANT]  
> To use this plugin, you need to have Laravel Reverb installed and enable FilamentPHP broadcasting in your application.

For the final step, you need to set up Laravel Reverb for your application. See [Reverb](https://laravel.com/docs/11.x/reverb) for more details. After that, enable broadcasting for your FilamentPHP application by following this [guide](https://laraveldaily.com/post/configure-laravel-reverb-filament-broadcasting) by Laravel Daily.

Then everytime you start your application in your local environment, you will need to run the following command to enable broadcasting:

```bash
php artisan reverb:start
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
