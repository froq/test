<?php declare(strict_types=1);
namespace froq\test\date;
use froq\date\{Formatter, FormatterException, Date, UtcDate};

class FormatterTest extends \PHPUnit\Framework\TestCase
{
    function test_construction() {
        $fmt = new Formatter();
        $this->assertSame([], $fmt->getIntl());
        $this->assertSame('', $fmt->getFormat());
        $this->assertSame('C.UTF-8', $fmt->getLocale());
    }

    function test_formatMethods() {
        $fmt = new Formatter();
        $when = '1990-01-09 23:30:11';
        $format = '%Y-%m-%d %T';

        $this->assertSame($when, $fmt->format($when, $format));

        $fmt = new Formatter(format: $format);
        $this->assertSame($when, $fmt->format($when));

        $date = new Date($when);
        $this->assertSame($date->format('Y-m-d H:i:s'), $fmt->format($date, $format));

        $date = new UtcDate($when);
        $this->assertSame($date->format('Y-m-d H:i:s'), $fmt->formatUtc($date, $format));

        try {
            $fmt = new Formatter();
            $fmt->format($when);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FormatterException::class, $e);
            $this->assertStringContainsString('No format yet', $e->getMessage());
        }

        try {
            $fmt = new Formatter();
            $fmt->format($when, 'invalid %O');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FormatterException::class, $e);
            $this->assertStringContainsString('Invalid format', $e->getMessage());
        }
    }
}