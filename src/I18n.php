<?php

namespace Openclerk;

/**
 * A simple class to support internationalisation (i18n)
 * through the t() method.
 * The locale needs to be loaded at runtime either through
 * session data, cookies, databases etc.
 * Also see the methods for getting the list of available locales.
 */
class I18n {
  static $locales = array();

  /**
   * Locales can be defined through a number of different ways at runtime;
   * one way is through component-discovery, for example.
   * Here, locales can be registered.
   */
  static function addAvailableLocale(Locale $locale) {
    self::$locales[$locale->getKey()] = $locale;
  }

  static function getAvailableLocales() {
    if (!isset(self::$locales['en'])) {
      self::$locales['en'] = new DefaultLocale();
    }

    return self::$locales;
  }

  static function resetAvailableLocales() {
    self::$locales = array();
  }

  /**
   * Get the current locale, or 'en' if none is defined.
   */
  static function get_current_locale() {
    return isset($_SESSION['locale']) ? $_SESSION['locale'] : 'en';
  }

  /**
   * @param $code string the locale code to set to, or {@code null} to reset it to default
   */
  static function setLocale($code = null) {
    if ($code === null) {
      $code = 'en';   // default locale
    }

    $locales = self::getAvailableLocales();
    if (!isset($locales[$code])) {
      throw new LocaleException("Locale '$code' does not exist");
    }
    $_SESSION['locale'] = $code;
  }

  /**
   * Translate a given 'en' string into the current locale. Loads locale data
   * from {@code __DIR__ . "/../locale/" . $locale . ".php"}.
   *
   * If the given string does not exist in the locale then
   * call {@code missing_locale_string($key, $locale)} (if the function exists)
   * and return the default translation (in 'en').
   *
   * @see set_locale($locale)
   * @see missing_locale_string($key, $locale)
   */
  static function t($category, $key = false, $args = array()) {
    if (is_string($category) && is_string($key)) {
      return self::tWithoutCategory($key, $args);
    } else {
      if ($key === false) {
        return self::tWithoutCategory($category);
      } else {
        return self::tWithoutCategory($category, $key);
      }
    }
  }

  static $default_keys = array();

  /**
   * Add default keys to all strings, e.g. :site_name
   */
  static function addDefaultKeys($keys) {
    self::$default_keys = array_merge(self::$default_keys, $keys);
  }

  static $global_loaded_locales = array();

  static function tWithoutCategory($key = false, $args = array()) {
    $locale = self::get_current_locale();

    // remove any unnecessary whitespace in the key that won't be displayed
    $key = self::strip_i18n_key($key);

    if ($locale != 'en' && !isset(self::$global_loaded_locales[$locale])) {
      if (!isset(self::$locales[$locale])) {
        throw new LocaleException("No known locale '$locale'");
      }

      $localeInstance = self::$locales[$locale];
      $loaded = $localeInstance->load();

      self::$global_loaded_locales[$locale] = $loaded;
    }

    if (!is_array($args)) {
      throw new LocaleException("Expected array argument");
    }
    foreach ($args as $k => $value) {
      if (is_numeric($k)) {
        throw new LocaleException("Did not expect numeric key '$k'");
      }
      if (substr($k, 0, 1) !== ":") {
        throw new LocaleException("Did not expect non-parameterised key '$k'");
      }
    }

    // add default arguments (but keep any existing keys used)
    $args += self::$default_keys;

    if (!isset(self::$global_loaded_locales[$locale][$key])) {
      if ($locale != 'en') {
        \Openclerk\Events::trigger('i18n_missing_string', array("locale" => $locale, "key" => $key));
      }
      // if (is_admin() && get_site_config('show_i18n')) {
      //   return "[" . strtr($key, $args) . "]";
      // } else {
        return strtr($key, $args);
      // }
    }
    // if (is_admin() && get_site_config('show_i18n')) {
    //   return "[" . strtr(self::$global_loaded_locales[$locale][$key], $args) . "]";
    // } else {
      return strtr(self::$global_loaded_locales[$locale][$key], $args);
    // }
  }

  /**
   * remove any unnecessary whitespace in the key that won't be displayed
   * @return the key with all leading, trailing and multiple inline spaces removed
   */
  static function strip_i18n_key($key) {
    $key = preg_replace("/[\r\n]+/im", " ", $key);
    $key = preg_replace("/[\\s]{2,}/im", " ", $key);
    return trim($key);
  }

  /**
   * Return the plural of something.
   * e.g. plural('book', 1), plural('book', 'books', 1), plural('book', 1000)
   */
  static function plural($string, $strings, $number = false, $decimals = 0) {
    // old format
    if (is_numeric($string)) {
      if ($number === false) {
        return self::plural($strings, $strings . "s", $string, $decimals);
      } else {
        return self::plural($strings, $number, $string, $decimals);
      }
    }

    // no second parameter provided
    if ($number === false) {
      return self::plural($string, $string . "s", $strings, $decimals);
    }

    if (floor($number) == 1) {
      return sprintf("%s %s", number_format(floor($number), $decimals), t($string));
    } else {
      return sprintf("%s %s", number_format(floor($number), $decimals), t($strings));
    }
  }


}
