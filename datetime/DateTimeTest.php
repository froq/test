<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{DateTime, DateTimeException, DateTimeZone, DateTimeZoneException,
    Timestamp, Interval};

class DateTimeTest extends \TestCase
{
    function testConstructor() {
        $dt = new DateTime();
        $this->assertInstanceOf(\DateTime::class, $dt);
        $this->assertInstanceOf(\Stringable::class, $dt);

        $dt = new DateTime($when = date('c'), 'UTC');
        $this->assertSame($when, $dt->toString('c'));
        $this->assertSame('UTC', $dt->getTimezone()->toString());

        $dt = new DateTime($when = time(), 'UTC');
        $this->assertSame($when, (int) $dt->format('U'));

        $dt = new DateTime($when = utime(), 'UTC');
        $this->assertSame($when, (float) $dt->format('U.u'));

        $dt = new DateTime('', 'default');
        $this->assertSame('UTC', $dt->getTimezone()->toString());

        try { // Invalid date/time.
            new DateTime('1 2 3 invalid');
        } catch (DateTimeException $e) {
            $this->assertSame('Unexpected character', $e->getMessage());
        }

        try { // Invalid date/time zone.
            new DateTime('', where: 'invalid');
        } catch (DateTimeException $e) {
            $this->assertSame('Unknown or bad timezone (invalid)', $e->getMessage());
            $this->assertInstanceOf(DateTimeZoneException::class, $e->getCause());
        }
    }

    function testMagicString() {
        $dt = new DateTime('1990-01-09 23:30:11.123456 Z');
        $this->assertSame('1990-01-09T23:30:11.123456Z', (string) $dt);
    }

    function testModify() {
        $dt = new DateTime('1990-01-09 23:30:11.506001 +00:00');
        $this->assertSame('1990-01-10T00:30:11+00:00', $dt->modify(3600)->format('c'));

        $dt = new DateTime('1990-01-09 23:30:11.506001 +00:00');
        $this->assertSame('1990-01-10T00:30:11+00:00', $dt->modify('+1 hour')->format('c'));

        $dt = new DateTime('1990-01-09 23:30:11.506001 +00:00');
        $this->assertSame('1990-01-10T00:30:11+00:00', $dt->modify(new Interval('PT1H'))->format('c'));

        $dt = new DateTime('1990-01-09 23:30:11.506001 +00:00');
        $this->assertSame('1990-01-10T00:30:11+00:00', $dt->modify(Interval::of(hour: 1))->format('c'));

        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessageMatches('~^Failed to parse time string~');
        $dt->modify('+1 hourzzz');
    }

    function testTimezoneMethods() {
        $dt = new DateTime('', 'Z');
        $this->assertSame('UTC', (string) $dt->getTimezone());
        $this->assertSame('UTC', (string) $dt->setTimezone('UTC')->getTimezone());
        $this->assertSame('UTC', (string) $dt->setTimezone(new DateTimeZone('UTC'))->getTimezone());

        $this->assertSame('UTC', $dt->getTimezoneId());
        $this->assertSame('UTC', $dt->getTimezoneAbbr());
        $this->assertSame('UTC', $dt->getTimezoneName());
        $this->assertEquals(new DateTimeZone('UTC'), $dt->getTimezone());
    }

    function testTimestampMethods() {
        $dt = new DateTime($ts = 1657045544);
        $this->assertSame($ts, $dt->getTimestamp());
        $this->assertSame($ts + 1, $dt->setTimestamp($ts + 1)->getTimestamp());

        $dt = new DateTime($ts = 1657045544.123456);
        $this->assertSame($ts, $dt->getTimestampMicros());
        $this->assertSame(~~($ts * 1000), $dt->getTimestampMillis());
    }

    function testOffsetMethods() {
        $dt = new DateTime();
        $this->assertSame(0, $dt->getOffset());
        $this->assertSame('+00:00', $dt->getOffsetCode());
    }

    function testDateMethods() {
        $dt = new DateTime();
        $dt->setDate('1990-01-09');
        $this->assertSame('1990-01-09', $dt->getDate());
        $this->assertSame('1990-01-09 '. $dt->format('H:i:s.u'), $dt->getFullDate());

        $dt->setDate('1990-1-9');
        $this->assertSame('1990-01-09', $dt->getDate());
        $this->assertSame('1990-01-09 '. $dt->format('H:i:s.u'), $dt->getFullDate());

        $dt->setDate(1990, 1, 9);
        $this->assertSame('1990-01-09', $dt->getDate());
        $this->assertSame('1990-01-09 '. $dt->format('H:i:s.u'), $dt->getFullDate());

        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessage("Invalid date: '1111' (use a parsable date, eg: 2022-01-01)");
        $dt->setDate('1111');
    }

    function testTimeMethods() {
        $dt = new DateTime();
        $dt->setTime('23:30:11.123456');
        $this->assertSame('23:30', $dt->getTime());
        $this->assertSame('23:30:11.123456', $dt->getFullTime());

        $dt->setTime(23, 30, 11, 123456);
        $this->assertSame('23:30', $dt->getTime());
        $this->assertSame('23:30:11.123456', $dt->getFullTime());

        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessage("Invalid time: '1111' (use a parsable time, eg: 22:11:19 or 22:11:19.123345)");
        $dt->setTime('1111');
    }

    function testAdd() {
        $dt = new DateTime('1990-01-09 23:30:11');
        $this->assertSame('1990-01-10 00:30:11', $dt->add(3600)->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-10 01:30:11', $dt->add('1 hour')->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-10 02:30:11', $dt->add(new Interval('PT1H'))->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-10 03:30:11', $dt->add(Interval::of(hour: 1))->format('Y-m-d H:i:s'));
    }

    function testSub() {
        $dt = new DateTime('1990-01-09 23:30:11');
        $this->assertSame('1990-01-09 22:30:11', $dt->sub(3600)->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-09 21:30:11', $dt->sub('1 hour')->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-09 20:30:11', $dt->sub(new Interval('PT1H'))->format('Y-m-d H:i:s'));
        $this->assertSame('1990-01-09 19:30:11', $dt->sub(Interval::of(hour: 1))->format('Y-m-d H:i:s'));
    }

    function testDiff() {
        $dt1 = new DateTime('1990-01-09 23:30:11.506001');
        $dt2 = new DateTime('1990-02-19 13:22:45.389718');

        $diff = $dt1->diff($dt2);

        $this->assertInstanceOf(Interval::class, $diff);
        $this->assertEquals($diff->toArray(), Interval::of(
            year: 0, month: 1, day: 9,
            hour: 13, minute: 52, second: 33,
            fraction: 0.883717, days: 40,
        )->toArray());

        $this->assertSame(0, $diff->getYear());
        $this->assertSame(1, $diff->getMonth());
        $this->assertSame(9, $diff->getDay());
        $this->assertSame(13, $diff->getHour());
        $this->assertSame(52, $diff->getMinute());
        $this->assertSame(33, $diff->getSecond());
        $this->assertSame(883717, $diff->getMicrosecond());
        $this->assertSame(0.883717, $diff->getFraction());
        $this->assertSame(40, $diff->getDays());
    }

    function testFormatMethods() {
        $dt = new DateTime($when = '1990-01-09 23:30:11');
        $this->assertSame($when, $dt->format('Y-m-d H:i:s'));
        $this->assertSame($when, $dt->formatUtc('Y-m-d H:i:s'));
        $this->assertSame($when, $dt->formatLocale('%Y-%m-%d %T'));

        $intl = $this->util('intl');

        // Locale/Intl.
        $this->assertSame('09 January 1990, 23:30', $dt->formatLocale('%d %B %Y, %R'));
        $this->assertSame('09 Ocak 1990, 23:30 ÖS', $dt->formatLocale('%d %B %Y, %R %p', 'tr_TR', $intl));
        $this->assertSame('09 Januar 1990, 23:30 ', $dt->formatLocale('%d %B %Y, %R %p', 'de_DE', $intl));

        // Ago.
        $dt = new DateTime('-1 day');
        $this->assertSame('Yesterday, ' . $dt->format('H:i'), $dt->formatAgo());
        $this->assertSame('Dün, ' . $dt->format('H:i'), $dt->formatAgo('tr_TR', $intl));
        $this->assertSame('Gestern, ' . $dt->format('H:i'), $dt->formatAgo('de_DE', $intl));

        // By (timezone).
        $dt = new DateTime('00:00');
        $this->assertSame('00:00', $dt->format('H:i'));
        $this->assertSame('03:00', $dt->formatBy('H:i', 'Europe/Istanbul'));
        $this->assertSame('03:00', $dt->formatBy('H:i', new DateTimeZone('Europe/Istanbul')));
    }

    function testStringMethods() {
        $dt = new DateTime($when = '1990-01-09T23:30:11.506001+03:00');

        $intl = $this->util('intl');

        $this->assertSame($when, $dt->toString());
        $this->assertSame('09 January 1990, 23:30', $dt->toLocaleString());
        $this->assertSame('09 Ocak 1990, 23:30', $dt->toLocaleString(null, 'tr_TR', $intl));
        $this->assertSame('09 Januar 1990, 23:30', $dt->toLocaleString(null, 'de_DE', $intl));

        $this->assertSame('1990-01-09T20:30:11.506001Z', $dt->toUtcString());
        $this->assertSame('09 January 1990, 20:30', $dt->toLocaleUtcString());
        $this->assertSame('09 Ocak 1990, 20:30', $dt->toLocaleUtcString(null, 'tr_TR', $intl));
        $this->assertSame('09 Januar 1990, 20:30', $dt->toLocaleUtcString(null, 'de_DE', $intl));

        $this->assertSame('1990-01-09T23:30:11.506001+03:00', $dt->toIsoString());
        $this->assertSame('Tue, 09 Jan 1990 20:30:11 GMT', $dt->toHttpString());
        $this->assertSame('Tue, 09 Jan 1990 20:30:11 GMT', $dt->toHttpCookieString());
        $this->assertSame('"1990-01-09T23:30:11.506001+03:00"', json_encode($dt));
    }

    function testToFromTimestamp() {
        $dt = new DateTime();
        $this->assertInstanceOf(Timestamp::class, $dt->toTimestamp());
        $this->assertSame(+$dt->format('U'), $dt->toTimestamp()->getTime());

        $this->assertInstanceOf(DateTime::class, DateTime::fromTimestamp(time()));
        $this->assertInstanceOf(DateTime::class, DateTime::fromTimestamp(utime()));
        $this->assertInstanceOf(DateTime::class, DateTime::fromTimestamp(new Timestamp()));
    }

    function testOf() {
        $dt = DateTime::of(1990, 1, 9, 23, 30, 11, 506001, 'UTC');
        $this->assertSame('1990-01-09T23:30:11.506001Z', $dt->toString());

        $dt = DateTime::of(1990, 1, 9, 23, 30, 11, 506001, 'Europe/Istanbul');
        $this->assertSame('1990-01-10T01:30:11.506001+02:00', $dt->toString());

        $dt = DateTime::of(1990, 1, 9, 23, 30, 11, 506001, 'Europe/Berlin');
        $this->assertSame('1990-01-10T00:30:11.506001+01:00', $dt->toString());
    }
}
