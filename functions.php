<?php

/**
 * Global i18n functions.
 */

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
function t($category, $key = false, $args = array()) {
  return Openclerk\I18n::t($category, $key, $args);
}

/**
 * Helper function for {@code htmlspecialchars(t(...))}.
 * @see t()
 */
function ht($category, $key = false, $args = array()) {
  return htmlspecialchars(t($category, $key, $args));
}

/**
 * Return the plural of something.
 * e.g. plural('book', 1), plural('book', 'books', 1), plural('book', 1000)
 */
function plural($string, $strings, $number = false, $decimals = 0) {
  return Openclerk\I18n::plural($string, $strings, $number, $decimals);
}
