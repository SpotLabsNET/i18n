<?php

namespace Openclerk;

class DefaultLocale implements Locale {

  /**
   * By default, the default locale is English.
   */
  function getKey() {
    return "en";
  }

  function getTitle() {
    return "English" /* i18n */;
  }

  function load() {
    throw new LocaleException("The DefaultLocale should never be loaded");
  }

}
