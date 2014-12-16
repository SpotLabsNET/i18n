openclerk/i18n
===============

A library for simple i18n management in PHP.

## Installing

Include `openclerk/i18n` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "openclerk/i18n": "dev-master"
  },
  "repositories": [{
    "type": "vcs",
    "url": "https://github.com/openclerk/i18n"
  }]
}
```

## Features

TODO

## Using

```php
I18n::addAvailableLocale('fr', new FrenchLocale());   // implement your own Locale here

echo t("hello");    // returns bonjour
```

You can also listen to the `i18n_missing_string` event (with _openclerk/events_)
to capture missing locale strings at runtime:

```php
\Openclerk\Events::on('i18n_missing_string', function($data) {
  echo $data['locale'] . ": " . $data['key'];
});

echo t("missing string");   // prints "fr: missing string"
```

## TODO

1. How to capture i18n strings with JSON?
1. How to merge multiple i18n projects together with _component-discovery_ or _asset-discovery_?
1. How to generate a list of i18n strings used in a project (e.g. as part of a build process)?
1. Tests
1. Publish on Packagist
