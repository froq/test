<?php declare(strict_types=1);
namespace test\froq\datetime\format;
use froq\datetime\{DateTime, UtcDateTime};
use froq\datetime\format\{Formatter, FormatterException, Format};
use froq\datetime\locale\{Locale, Intl};

class FormatterTest extends \TestCase
{
    function testConstructor() {
        $formatter = new Formatter();
        $this->assertSame('', $formatter->getFormat());
        $this->assertSame('', $formatter->getLocale());
        $this->assertSame([], $formatter->getIntl());

        $formatter = new Formatter('%A', 'tr_TR.UTF-8', []);
        $this->assertSame('%A', $formatter->getFormat());
        $this->assertSame('tr_TR.UTF-8', $formatter->getLocale());
        $this->assertSame([], $formatter->getIntl());

        $formatter = new Formatter(new Format('%A'), new Locale('tr', 'TR', 'UTF-8'), new Intl([]));
        $this->assertSame('%A', $formatter->getFormat());
        $this->assertSame('tr_TR.UTF-8', $formatter->getLocale());
        $this->assertSame([], $formatter->getIntl());
    }

    function testSettersGetters() {
        $formatter = new Formatter();
        $this->assertSame('', $formatter->getFormat());
        $this->assertSame('', $formatter->getLocale());
        $this->assertSame([], $formatter->getIntl());

        $formatter->setFormat('%A')
                  ->setLocale('tr_TR')
                  ->setIntl(['today' => 'Bugün']);

        $this->assertSame('%A', $formatter->getFormat());
        $this->assertSame('tr_TR', $formatter->getLocale());
        $this->assertSame(['today' => 'Bugün'], $formatter->getIntl());
    }

    function testFormat() {
        $when = '1990-01-09 23:30:11';
        $format = 'Y-m-d H:i:s';

        $formatter = new Formatter($format);
        $this->assertSame('1990-01-09 23:30:11', $formatter->format($when));
        $this->assertSame('1990-01-10 01:30:11', $formatter->format(new DateTime($when, 'Europe/Istanbul')));
    }

    function testFormatUtc() {
        $when = '1990-01-09 23:30:11';
        $format = 'Y-m-d H:i:s';

        $formatter = new Formatter($format);
        $this->assertSame('1990-01-09 23:30:11', $formatter->formatUtc($when));
        $this->assertSame('1990-01-09 23:30:11', $formatter->formatUtc(new DateTime($when, 'Europe/Istanbul')));
    }

    function testFormatLocale() {
        $when = '1990-01-09 23:30:11';
        $format = '%Y-%m-%d %T';

        $formatter = new Formatter($format);
        $this->assertSame('1990-01-09 23:30:11', $formatter->formatLocale($when));
        $this->assertSame('1990-01-10 01:30:11', $formatter->formatLocale(new DateTime($when, 'Europe/Istanbul')));

        $intl = $this->util('intl');
        $dt = new DateTime($when);

        $formatter = new Formatter();
        $this->assertSame('09 January 1990, 23:30', $formatter->formatLocale($dt, '%d %B %Y, %R'));

        $formatter = new Formatter('', 'tr_TR', $intl);
        $this->assertSame('09 Ocak 1990, 23:30 ÖS', $formatter->formatLocale($dt, '%d %B %Y, %R %p'));

        $formatter = new Formatter('', 'de_DE', $intl);
        $this->assertSame('09 Januar 1990, 23:30 ', $formatter->formatLocale($dt, '%d %B %Y, %R %p'));
    }

    function testFormatLocaleUtc() {
        $when = '1990-01-09 23:30:11';
        $format = '%Y-%m-%d %T';

        $formatter = new Formatter($format);
        $this->assertSame('1990-01-09 23:30:11', $formatter->formatLocaleUtc($when));
        $this->assertSame('1990-01-09 23:30:11', $formatter->formatLocaleUtc(new DateTime($when, 'Europe/Istanbul')));

        $intl = $this->util('intl');
        $dt = new DateTime($when);

        $formatter = new Formatter();
        $this->assertSame('09 January 1990, 23:30', $formatter->formatLocaleUtc($dt, '%d %B %Y, %R'));

        $formatter = new Formatter('', 'tr_TR', $intl);
        $this->assertSame('09 Ocak 1990, 23:30 ÖS', $formatter->formatLocaleUtc($dt, '%d %B %Y, %R %p'));

        $formatter = new Formatter('', 'de_DE', $intl);
        $this->assertSame('09 Januar 1990, 23:30 ', $formatter->formatLocaleUtc($dt, '%d %B %Y, %R %p'));
    }

    function testFormatAgo() {
        $intl = $this->util('intl');
        $dt = new DateTime('-1 day');

        $formatter = new Formatter();
        $this->assertSame('Yesterday, ' . $formatter->format($dt, 'H:i'), $formatter->formatAgo($dt));

        $formatter = new Formatter('', 'tr_TR', $intl);
        $this->assertSame('Dün, ' . $formatter->format($dt, 'H:i'), $formatter->formatAgo($dt));

        $formatter = new Formatter('', 'de_DE', $intl);
        $this->assertSame('Gestern, ' . $formatter->format($dt, 'H:i'), $formatter->formatAgo($dt));
    }

    function testExceptions() {
        $when = '1990-01-09 23:30:11';

        try {
            $formatter = new Formatter();
            $formatter->format($when);
        } catch (FormatterException $e) {
            $this->assertStringContains('No format yet', $e->getMessage());
        }

        try {
            $formatter = new Formatter();
            $formatter->format($when, '%O');
        } catch (FormatterException $e) {
            $this->assertStringContains('Invalid format', $e->getMessage());
        }
    }
}
