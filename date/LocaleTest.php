<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{Locale, LocaleException, LocaleInfo};

class LocaleTest extends \TestCase
{
    function test_construction() {
        $locale = new Locale('en_US.UTF-8');
        $this->assertSame('en_US.UTF-8', (string) $locale);
        $this->assertSame('en', $locale->getLanguage());
        $this->assertSame('US', $locale->getCountry());
        $this->assertSame('UTF-8', $locale->getEncoding());

        $this->assertEquals(new LocaleInfo(
            language: 'en', country: 'US', encoding: 'UTF-8'
        ), $locale->info);
        $this->assertInstanceOf(LocaleInfo::class, $locale->info);

        try {
            new Locale('');
        } catch (LocaleException $e) {
            $this->assertSame('Empty locale given', $e->getMessage());
        }

        try {
            new Locale('en_');
        } catch (LocaleException $e) {
            $this->assertSame('Invalid locale: en_', $e->getMessage());
        }
    }

    function test_make() {
        $locale = Locale::make('en', 'US', null);
        $this->assertSame('en_US', (string) $locale);
        $this->assertSame('en', $locale->getLanguage());
        $this->assertSame('US', $locale->getCountry());
        $this->assertNull($locale->getEncoding());
        $this->assertInstanceOf(Locale::class, $locale);
    }

    function test_makeInfo() {
        $localeInfo = Locale::makeInfo('en_US');
        $this->assertSame('en', $localeInfo->language);
        $this->assertSame('US', $localeInfo->country);
        $this->assertNull($localeInfo->encoding);
        $this->assertInstanceOf(LocaleInfo::class, $localeInfo);
    }

    function test_parse() {
        $ret = Locale::parse('en_US');
        $this->assertSame('en', $ret['language']);
        $this->assertSame('US', $ret['country']);
        $this->assertNull($ret['encoding']);
    }
}
