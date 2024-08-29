> [!WARNING]
> This package is abandoned, you should avoid using it.
>
> Use [localization feature](https://laravel.com/docs/master/localization) of [laravel/framework](https://github.com/laravel/framework) instead.

# Laravel I18n

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/HealthEngineAU/laravel-i18n/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/HealthEngineAU/laravel-i18n/tree/main)

This is a custom package designed for Laravel Eloquent. It provides helpers used to manage flat JSON files for
localisation.

## Setup

Configure composer repository:

```shell
composer config repositories.healthengine/laravel-i18n git git@github.com:HealthEngineAU/laravel-i18n.git
```

Install:

```shell
composer require healthengine/laravel-i18n
```

Update providers in `config/app.php` to replace Laravel's stock translation provider with the one of this library:

```php
<?php

use Illuminate\Support\ServiceProvider;

return [
    // ...

    'providers' => ServiceProvider::defaultProviders()->merge([
        // ...
    ])->replace([
        Illuminate\Translation\TranslationServiceProvider::class => Healthengine\I18n\TranslationServiceProvider::class,
    ]),

    // ...
];
```

Publish resources:

```shell
php artisan vendor:publish --provider="Healthengine\I18n\TranslationServiceProvider"
```

## Usage

### Multiple JSON files

Each language can have multiple JSON translation files.
```
resources/
  lang/
    en/
      base.json
      base.generated.json
      other.json
    zh-cn/
      base.json
      base.generated.json
```

### i18n() helper

#### i18next placeholders

You can define strings in your language files using `i18next` style placeholders.

```json
{
  "my.greeting": "Hello, {{name}}!"
}
```
and then use:
```php
i18n('greeting', ['name' => 'Bernadette'])
// -> "Hello, Bernadette!"
```

#### Markup

Basic support for HTML markup placeholders:

```json
{
  "text.with.link": "Hi, <0>{{name}}</0>. Click <1>here</1> to continue."
}
```
```php
i18n('text.with.link', ['name' => 'Bernadette'], ['<b>', '<a href="#target" />'])
// -> 'Hi, <b>Bernadette</b>. Click <a href="#target">here</a> to continue.'
```

### Middleware

You can add any of the attached middlwares to associate languages with requests:

* **AcceptLanguage** - Set the language from the HTTP `Accept-Language` header.
* **HasLanguage** - Use the language from the request parameter `lang` (and store it as a cookie).
* **DetectLanguage** - Automatically determine the best language.

```php
protected $routeMiddleware = [
    'accept-lang' => \Healthengine\I18n\Middleware\AcceptLanguage::class,
    'has-lang' => \Healthengine\I18n\Middleware\HasLanguage::class,
    'detect-lang' => \Healthengine\I18n\Middleware\DetectLanguage::class,
];

```
