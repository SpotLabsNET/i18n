<?php

use Openclerk\I18n;
use Openclerk\LocaleException;
use Openclerk\Locale;

class TestFrenchLocale implements Locale {
  function getKey() {
    return "fr";
  }

  function getTitle() {
    return "French";
  }

  function load() {
    return array(
      "account" => "compte",
      "accounts" => "comptes",
      "address" => "adresse",
      "addresses" => "adresses",
    );
  }
}

/**
 * Set the locale to French, and do all our
 * tests again.
 */
class FrenchTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    I18n::addAvailableLocale(new TestFrenchLocale());
    I18n::setLocale('fr');
  }

  function tearDown() {
    I18n::setLocale(null);
    I18n::resetAvailableLocales();
  }

  /**
   * Tests {@link t()} functionality.
   * We're testing the search/replace functionality rather than locale loading at this point.
   */
  function testTStrtr() {
    $this->assertEquals("Hello meow 1", t("Hello :world 1", array(':world' => 'meow')));
    $this->assertEquals(":hello :hi 2", t(":hi :hello 2", array(':hi' => ':hello', ':hello' => ':hi')));
    $this->assertEquals("Hello :world 3", t("Hello :world 3", array(':meow' => ':world')));
    $this->assertEquals("Hello :world 4", t("Hello :world 4"));
    $this->assertEquals("Hello :world 5", t("Hello   :world \r\n 5"));

    // these should all throw exceptions
    try {
      $this->assertEquals("Hello meow", t("Hello :world", array('test')));
      $this->fail("Expected LocaleException");
    } catch (LocaleException $e) {
      // expected
    }
  }

  /**
   * Tests {@link t()} functionality, that the developer can also specify
   * a category as part of the function.
   */
  function testTCategory() {
    $this->assertEquals("Hello meow 1", t("test", "Hello :world 1", array(':world' => 'meow')));
    $this->assertEquals(":hello :hi 2", t("test", ":hi :hello 2", array(':hi' => ':hello', ':hello' => ':hi')));
    $this->assertEquals("Hello :world 3", t("test", "Hello :world 3", array(':meow' => ':world')));
    $this->assertEquals("Hello :world 4", t("test", "Hello :world 4"));
    $this->assertEquals("Hello :world 5", t("test", "Hello   :world \r\n 5"));

    // these should all throw exceptions
    try {
      $this->assertEquals("Hello meow", t("test", "Hello :world", array('test')));
      $this->fail("Expected LocaleException");
    } catch (LocaleException $e) {
      // expected
    }
  }

  /**
   * A default locale is included.
   */
  function testAllLocalesIncludesDefault() {
    foreach (I18n::getAvailableLocales() as $localeInstance) {
      if ($localeInstance instanceof Openclerk\DefaultLocale) {
        return;
      }
    }
    $this->fail("Could not find DefaultLocale");
  }

  /**
   * Tests the {@link plural()} function.
   */
  function testPlural() {
    $this->assertEquals("1 compte", plural("account", 1));
    $this->assertEquals("2 comptes", plural("account", 2));
    $this->assertEquals("1 compte", plural("account", "accounts", 1));
    $this->assertEquals("9 comptes", plural("account", "accounts", 9));
    $this->assertEquals("1,000 comptes", plural("account", "accounts", 1000));
    $this->assertEquals("9 adresses", plural("address", "addresses", 9));
  }

  /**
   * Tests the {@link plural()} function in the old calling style.
   */
  function testPluralOld() {
    $this->assertEquals("1 compte", plural(1, "account"));
    $this->assertEquals("2 comptes", plural(2, "account"));
    $this->assertEquals("1 compte", plural(1, "account", "accounts"));
    $this->assertEquals("9 comptes", plural(9, "account", "accounts"));
    $this->assertEquals("1,000 comptes", plural(1000, "account", "accounts"));
    $this->assertEquals("9 adresses", plural(9, "address", "addresses"));
  }

  /**
   * It does not translate strings that have no matching translation.
   */
  function testMissingString() {
    $this->assertEquals("missing string", t("missing string"));
  }

  /**
   * However we can capture missing strings using the event framework.
   */
  function testEventIsThrown() {
    global $_test_event_is_thrown;
    $_test_event_is_thrown = false;
    $handler = \Openclerk\Events::on('i18n_missing_string', function($string) {
      global $_test_event_is_thrown;
      $_test_event_is_thrown = $string;
    });

    t("a missing string");
    $this->assertEquals(array("locale" => "fr", "key" => "a missing string"), $_test_event_is_thrown);

    // and be a good events citizen
    \Openclerk\Events::unbind($handler);
  }

}
