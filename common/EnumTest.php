<?php declare(strict_types=1);
namespace froq\test\common;
use froq\common\object\{Enum, EnumException};

class EnumTest extends \TestCase
{
    function test_valueMethods() {
        $enum = $this->getMock();

        $enum->value(Colors::BLACK);
        $this->assertSame(Colors::BLACK, $enum->value());

        $enum->setValue(Colors::WHITE);
        $this->assertSame(Colors::WHITE, $enum->getValue());
    }

    function test_arrayMethods() {
        $enum = $this->getMock();
        $consts = ['BLACK' => Colors::BLACK, 'WHITE' => Colors::WHITE,
                   'GREEN' => Colors::GREEN];

        $this->assertSame($consts, $enum::all());
        $this->assertSame($consts, $enum::toArray());
        $this->assertSame(array_keys($consts), $enum::names());
        $this->assertSame(array_values($consts), $enum::values());

        $this->assertSame($consts, Colors::all());
        $this->assertSame($consts, Colors::toArray());
        $this->assertSame(array_keys($consts), Colors::names());
        $this->assertSame(array_values($consts), Colors::values());
    }

    function test_checkerMethods() {
        $this->assertTrue(Colors::validName('BLACK'));
        $this->assertFalse(Colors::validName('black'));
        $this->assertTrue(Colors::validName('black', upper: true));

        $this->assertTrue(Colors::validValue(1));
        $this->assertFalse(Colors::validValue(10));
    }

    function test_getterMethods() {
        $this->assertSame('BLACK', Colors::nameOf(Colors::BLACK));
        $this->assertSame(null, Colors::nameOf('NONE'));

        $this->assertSame(Colors::BLACK, Colors::valueOf('BLACK'));
        $this->assertSame(null, Colors::valueOf('black'));
        $this->assertSame(Colors::BLACK, Colors::valueOf('black', upper: true));
    }

    function test_callMagicMethod() {
        $enum = $this->getMock(Colors::BLACK);

        $this->assertTrue($enum->isBlack());
        $this->assertTrue($enum::isBlack(Colors::BLACK));
        $this->assertTrue(Colors::isBlack(Colors::BLACK));

        $noCallMessage = 'No valid call';
        $noConstMessage = 'No constant exists';

        try {
            $enum->foo();
        } catch (EnumException $e) {
            $this->assertStringContains($noCallMessage, $e->getMessage());
        }

        try {
            $enum->isBlue();
        } catch (EnumException $e) {
            $this->assertStringContains($noConstMessage, $e->getMessage());
        }

        try {
            Colors::foo();
        } catch (EnumException $e) {
            $this->assertStringContains($noCallMessage, $e->getMessage());
        }

        try {
            Colors::isBlue();
        } catch (EnumException $e) {
            $this->assertStringContains($noConstMessage, $e->getMessage());
        }
    }

    private function getMock($value = null) {
        return new Colors($value);
    }
}

class Colors extends Enum {
    const BLACK = 1;
    const WHITE = 2;
    const GREEN = 3;
}
