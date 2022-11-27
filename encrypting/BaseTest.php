<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Base, BaseException};

class BaseTest extends \TestCase
{
    function test_encode() {
        $input = 'Hello!';
        $base62Output = 'mBps3ubT';
        $base36Output = 's7rxvhd0h';

        $this->assertSame('', Base::encode(''));
        $this->assertSame($base62Output, Base::encode($input));
        $this->assertSame($base36Output, Base::encode($input, Base::BASE36_CHARS));

        try {
            Base::encode($input, '');
        } catch (BaseException $e) {
            $this->assertStringContains('Characters cannot be empty', $e->getMessage());
        }

        try {
            Base::encode($input, 'a');
        } catch (BaseException $e) {
            $this->assertStringContains('Characters length must be between 2-256', $e->getMessage());
        }
    }

    function test_decode() {
        $output = 'Hello!';
        $base62Input = 'mBps3ubT';
        $base36Input = 's7rxvhd0h';

        $this->assertSame('', Base::decode(''));
        $this->assertSame($output, Base::decode($base62Input));
        $this->assertSame($output, Base::decode($base36Input, Base::BASE36_CHARS));

        try {
            Base::decode($output, '');
        } catch (BaseException $e) {
            $this->assertStringContains('Characters cannot be empty', $e->getMessage());
        }

        try {
            Base::decode($output, 'a');
        } catch (BaseException $e) {
            $this->assertStringContains('Characters length must be between 2-256', $e->getMessage());
        }

        try {
            Base::decode('ABCabc', Base::BASE36_CHARS);
        } catch (BaseException $e) {
            $this->assertStringContains("Invalid characters 'ABC' found", $e->getMessage());
        }
    }

    function test_convertions() {
        $num = 123456; $hex = '1e240';

        $this->assertSame($hex, Base::toBase($num, 16));
        $this->assertSame($num, Base::fromBase($hex, 16));
    }

    function test_characters() {
        $this->assertSame('0123456789', Base::chars(10));
        $this->assertSame(Base::BASE10_CHARS, Base::chars(10));

        foreach ([10, 16, 36, 62] as $base) {
            $const = constant(Base::class . '::BASE' . $base . '_CHARS');
            $this->assertSame($const, Base::chars($base));
        }
    }
}
