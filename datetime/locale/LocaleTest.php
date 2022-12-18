<?php declare(strict_types=1);
namespace test\froq\datetime\locale;
use froq\datetime\locale\{Locale, LocaleException};

class LocaleTest extends \TestCase
{
    function testConstructor() {
        $locale = new Locale('en', 'US', 'UTF-8');
        $this->assertSame('en_US.UTF-8', (string) $locale);
        $this->assertSame('en', $locale->language);
        $this->assertSame('US', $locale->country);
        $this->assertSame('UTF-8', $locale->encoding);
        $this->assertSame(null, $locale->currency);
        $this->assertSame('TIME', $locale->category->name);
        $this->assertSame(LC_TIME, $locale->category->value);

        try {
            new Locale('');
        } catch (LocaleException $e) {
            $this->assertSame("Invalid language: ''", $e->getMessage());
        }

        try {
            new Locale('en_');
        } catch (LocaleException $e) {
            $this->assertSame("Invalid language: 'en_'", $e->getMessage());
        }
    }

    function testFrom() {
        $locale = Locale::from('en_US.UTF-8');
        $this->assertSame('en_US.UTF-8', (string) $locale);
        $this->assertInstanceOf(Locale::class, $locale);

        try {
            Locale::from('en_');
        } catch (LocaleException $e) {
            $this->assertSame("Invalid language: ''", $e->getMessage());
        }
    }

    function testFromTag() {
        $locale = Locale::fromTag('en-US', 'UTF-8');
        $this->assertSame('en_US.UTF-8', (string) $locale);
        $this->assertInstanceOf(Locale::class, $locale);

        try {
            Locale::from('en-');
        } catch (LocaleException $e) {
            $this->assertSame("Invalid language: ''", $e->getMessage());
        }
    }

    function testDefaultMethods() {
        $this->assertSame('en_US', Locale::setDefault('en_US'));
        $this->assertSame('en_US', Locale::getDefault());
    }
}
