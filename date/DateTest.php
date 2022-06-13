<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{Date, DateException, TimezoneException, One, Diff};

class DateTest extends \TestCase
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
        } catch (TimezoneException $e) {
            $this->assertStringContains('Invalid timezone', $e->getMessage());
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

    function test_modify() {
        $date = new Date('1990-01-09 23:30:11.506001 +00:00');
        $this->assertSame('1990-01-10T00:30:11+00:00', $date->modify('+1 hour')->format('c'));

        $this->expectException(DateException::class);
        $this->expectExceptionMessage('Failed to modify date');
        $date->modify('+1 hourzzz');
    }

    function test_diff() {
        $date1 = new Date('1990-01-09 23:30:11.506001 +00:00');
        $date2 = new Date('1990-02-19 13:22:45.389718 +00:00');

        $diff = $date1->diff($date2);

        $this->assertInstanceOf(Diff::class, $diff);
        $this->assertEquals($diff, new Diff(
            year: 0, month: 1, day: 9, days: 40,
            hour: 13, minute: 52, second: 33, microsecond: 883717
        ));

        $this->assertSame($diff->year, 0);
        $this->assertSame($diff->month, 1);
        $this->assertSame($diff->day, 9);
        $this->assertSame($diff->days, 40);
        $this->assertSame($diff->hour, 13);
        $this->assertSame($diff->minute, 52);
        $this->assertSame($diff->second, 33);
        $this->assertSame($diff->microsecond, 883717);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property froq\date\Diff::$year');
        $diff->year = 1;
    }

    function test_staticMethods() {
        $this->assertSame(time(), Date::now());
        $this->assertSame(One::MINUTE, Date::interval('+1 minute'));
    }
}
