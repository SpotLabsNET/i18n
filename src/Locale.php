<?php

namespace Openclerk;

interface Locale {

  /**
   * @return a string representing the locale, e.g. ISO code
   */
  function getKey();

  /**
   * Load the locale data at runtime. This method should only
   * be called once per request at most.
   * @return an array of (keys => values) which can be cached.
   */
  function load();

}
