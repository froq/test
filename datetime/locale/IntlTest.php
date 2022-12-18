<?php declare(strict_types=1);
namespace test\froq\datetime\locale;
use froq\datetime\locale\Intl;

class IntlTest extends \TestCase
{
    function testConstructor() {
        $intl = new Intl($translations = $this->util('intl'));
        $this->assertSame($translations, (array) $intl);
        $this->assertInstanceOf(\XArrayObject::class, $intl);
        $this->assertInstanceOf(\ArrayObject::class, $intl);
    }

    function testSettersGetters() {
        $intl = new Intl();
        $this->assertSame([], $intl->getTranslations());
        $this->assertSame(['foo' => 'Foo!'], $intl->setTranslations(['foo' => 'Foo!'])->getTranslations());

        $intl = new Intl();
        $this->assertNull($intl->getTranslation('en_US'));
        $this->assertFalse($intl->hasTranslation('en_US'));
        $this->assertTrue($intl->setTranslation('en_US', ['a' => 'b'])->hasTranslation('en_US'));
        $this->assertSame(['a' => 'b'], $intl->getTranslation('en_US'));

        $translations = $this->util('intl');

        $intl = new Intl();
        $this->assertNull($intl->getDays('tr_TR'));
        $this->assertNull($intl->getMonths('tr_TR'));
        $this->assertNull($intl->getPeriods('tr_TR'));

        $intl->setDays('tr_TR', $translations['tr_TR']['days'])
             ->setMonths('tr_TR', $translations['tr_TR']['months'])
             ->setPeriods('tr_TR', $translations['tr_TR']['periods']);

        $this->assertSame($translations['tr_TR']['days'], $intl->getDays('tr_TR'));
        $this->assertSame($translations['tr_TR']['months'], $intl->getMonths('tr_TR'));
        $this->assertSame($translations['tr_TR']['periods'], $intl->getPeriods('tr_TR'));
    }
}
