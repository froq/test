<?php declare(strict_types=1);
namespace froq\test\date;
use froq\date\{Date, DateException, TimezoneException};

class DateTest extends \PHPUnit\Framework\TestCase
{
    function test_constructor() {
        $date = new Date($when = date('c'), 'UTC');
        $this->assertSame($when, $date->format('c'));
        $this->assertSame('UTC', $date->getTimezone());

        $date = new Date($when = time(), 'UTC');
        $this->assertSame($when, (int) $date->format('U'));

        $date = new Date($when = microtime(true), 'UTC');
        $this->assertSame($when, (float) $date->format('U.u'));

        try {
            new Date('', where: 'invalid');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(TimezoneException::class, $e);
            $this->assertStringContainsString('Invalid timezone', $e->getMessage());
        }
    }

    function test_formatMethods() {
        $date = new Date($when = '1990-01-09 23:30:11');
        $format = 'Y-m-d H:i:s';
        $localeFormat = '%Y-%m-%d %T';

        $date->setFormat($format)
             ->setLocaleFormat($localeFormat);

        $this->assertSame($when, $date->format());
        $this->assertSame($when, $date->formatLocale());

        $date = new Date($when = '1990-01-09 23:30:11');

        $this->assertSame($when, $date->format($format));
        $this->assertSame($when, $date->formatLocale($localeFormat));
    }

    function test_convertMethods() {
        $date = new Date($when = '1990-01-09 23:30:11.506001 +00:00');

        $this->assertSame(631927811, $date->toInt());
        $this->assertSame(631927811.506001, $date->toFloat());

        $this->assertSame($when, $date->toString());
        $this->assertSame('09 January 1990, 23:30', $date->toLocaleString());
        $this->assertSame('1990-01-09T23:30:11.506001Z', $date->toUtcString());
        $this->assertSame('1990-01-09T23:30:11.506001Z', $date->toIsoString());
        $this->assertSame('Tue, 09 Jan 1990 23:30:11 GMT', $date->toHttpString());
        $this->assertSame('Tue, 09 Jan 1990 23:30:11 GMT', $date->toHttpCookieString());

        $dateInfo = [
            'date' => '1990-01-09T23:30:11.506001Z',
            'dateLocale' => '09 January 1990, 23:30',
            'time' => 631927811, 'utime' => 631927811.506001,
            'offset' => 0, 'offsetCode' => '+00:00',
            'zone' => 'UTC', 'locale' => 'C',
        ];
        $dateJson = '"1990-01-09T23:30:11.506001Z"';

        $this->assertSame($dateInfo, $date->toArray());
        $this->assertSame($dateJson, json_encode($date));
    }
}
