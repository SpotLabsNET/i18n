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

## Providing i18n strings

One easy way to implement a Locale is simply to define it in a JSON file:

```php
class French implements \Openclerk\Locale {

  function getKey() {
    return 'fr';
  }

  function load() {
    $json = json_decode(__DIR__ . "/fr.json", true /* assoc */);
    return $json;
  }

}
```

For speed, you could also define this as a PHP file `require()` instead.

## Providing i18n strings across multiple components and projects

By using [component-discovery](https://github.com/soundasleep/component-discovery) along with
[translation-discovery](https://github.com/soundasleep/translation-discovery), you can combine
translation files across multiple projects and Composer dependencies at build time. For example:

```php
abstract class DiscoveredLocale implements \Openclerk\Locale {

  function __construct($code, $file) {
    $this->code = $code;
    $this->file = $file;
  }

  function getKey() {
    return $this->code;
  }

  function load() {
    if (!file_exists($this->file)) {
      throw new \Openclerk\LocaleException("Could not find locale file for '" . $this->file . "'");
    }
    $result = array();
    require($this->file);
    return $result;
  }

}

class French extends DiscoveredLocale {

  public function __construct() {
    parent::__construct('fr', __DIR__ . "/../site/generated/translations/fr.php");
  }

}

\Openclerk\I18n::addAvailableLocales(DiscoveredComponents\Locales::getAllInstances());
```

(TODO: Add link to example project)

## TODO

1. How to generate a list of i18n strings used in a project (e.g. as part of a build process)?
1. Tests
1. Publish on Packagist
