# Laravel I18n
This is a custom package designed for Laravel Eloquent. It provides helpers used to manage flat JSON files for
localisation.

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
  "text.with.link": "Hi, {{name}}. Click <0>here</0> to continue."
}
```
```php
i18n('text.with.link', ['name' => 'Bernadette'], ['<a href="#target"'>, '</a>'])
// -> 'Hi, Bernadette. Click <a href="#target">here</a> to continue.'
```

### Middleware

You can add any of the attached middlwares to associate languages with requests:

* **AcceptLanguage** - Set the language from the HTTP `Accept-Language` header.
* **SetLanguage** - Set the language from the request parameter `lang`, and store it as a cookie.
* **DetectLanguage** - Automatically determine the best language.

```php
protected $routeMiddleware = [
    'accept-lang' => \HealthEngine\I18n\Middleware\AcceptLanguage::class,
    'has-lang' => \HealthEngine\I18n\Middleware\HasLanguage::class,
    'detect-lang' => \HealthEngine\I18n\Middleware\DetectLanguage::class,
];

```
