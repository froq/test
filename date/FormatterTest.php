<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{Formatter, FormatterException, Intl, Format, Locale, Date, UtcDate};

class FormatterTest extends \TestCase
{
    function test_construction() {
        $fmt = new Formatter();
        $this->assertSame([], $fmt->getIntl());
        $this->assertSame('', $fmt->getFormat());
        $this->assertSame('C.UTF-8', $fmt->getLocale());

        $fmt = new Formatter(new Intl([]), new Format('%A'), new Locale('tr_TR.UTF-8'));
        $this->assertSame([], $fmt->getIntl());
        $this->assertSame('%A', $fmt->getFormat());
        $this->assertSame('tr_TR.UTF-8', $fmt->getLocale());
    }

    function test_formatMethods() {
        $fmt = new Formatter();
        $when = '1990-01-09 23:30:11';
        $format = '%Y-%m-%d %T';

        $this->assertSame($when, $fmt->format($when, $format));

        $fmt = new Formatter(format: $format);
        $this->assertSame($when, $fmt->format($when));

        $fmt = new Formatter(format: new Format($format));
        $this->assertSame($when, $fmt->format($when));

        $date = new Date($when);
        $this->assertSame($date->format('Y-m-d H:i:s'), $fmt->format($date, $format));

        $date = new UtcDate($when);
        $this->assertSame($date->format('Y-m-d H:i:s'), $fmt->formatUtc($date, $format));

        try {
            $fmt = new Formatter();
            $fmt->format($when);
        } catch (FormatterException $e) {
            $this->assertStringContains('No format yet', $e->getMessage());
        }

        try {
            $fmt = new Formatter();
            $fmt->format($when, '%O');
        } catch (FormatterException $e) {
            $this->assertStringContains('Invalid format', $e->getMessage());
        }
    }
}
