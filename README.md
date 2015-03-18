openclerk/i18n [![Build Status](https://travis-ci.org/openclerk/i18n.svg?branch=master)](https://travis-ci.org/openclerk/i18n)
==============

A library for simple i18n management in PHP.

## Installing

Include `openclerk/i18n` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "openclerk/i18n": "dev-master"
  }
}
```

## Features

TODO

## Using

```php
I18n::addAvailableLocale(new FrenchLocale());   // implement your own Locale here

I18n::setLocale('fr');
echo t("hello");                  // returns 'bonjour'
echo I18n::getCurrentLocale();    // returns 'fr'
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
class FrenchLocale implements \Openclerk\Locale {

  function getKey() {
    return 'fr';
  }

  function getTitle() {
    return 'French' /* i18n */;
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

class FrenchLocale extends DiscoveredLocale {

  public function __construct() {
    parent::__construct('fr', __DIR__ . "/../site/generated/translations/fr.php");
  }

}

\Openclerk\I18n::addAvailableLocales(DiscoveredComponents\Locales::getAllInstances());
```

(TODO: Add link to example project)

## Persisting locale across sessions

When changing user locale, add a cookie:

```php
setcookie('locale', $locale, time() + (60 * 60 * 24 * 365 * 10) /* 10 years in the future */);
```

And then check for this cookie as necessary:

```php
if (isset($_COOKIE["locale"]) && in_array($_COOKIE["locale"], array_keys(I18n::getAvailableLocales()))) {
  I18n::setLocale($_COOKIE["locale"]);
}
```

## Discovering translation strings

The [soundasleep/translation-discovery](https://github.com/soundasleep/translation-discovery) project
has a `find` script that can be used to search your project for translation strings that may need to
be translated across all of your components.

This script will find all instances of the following translation strings, and output them to
the `template` JSON folder:

1. `t("string")`
1. `ht("string")`
1. `plural("string", 1)` and `plural("string", "strings", 1)`
1. `"string" /* i18n */`
1. And the single-quote versions of these patterns
