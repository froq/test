<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Suid, SuidException};

class SuidTest extends \TestCase
{
    function testGenerate() {
        $this->assertLength(10, Suid::generate(10));
        $this->assertMatches('~^[a-zA-Z0-9]+$~', Suid::generate(10));
        $this->assertMatches('~^[a-f0-9]+$~', Suid::generate(10, base: 16));
        $this->assertMatches('~^[a-z0-9]+$~', Suid::generate(10, base: 36));

        try {
            Suid::generate(0);
        } catch (SuidException $e) {
            $this->assertSame('Argument $length must be greater than 1, 0 given', $e->getMessage());
        }

        try {
            Suid::generate(10, base: 1);
        } catch (SuidException $e) {
            $this->assertSame('Argument $base must be between 2-62, 1 given', $e->getMessage());
        }
    }

    function testGenerateHexes() {
        $this->assertLength(10, Suid::generateHexes(10));
        $this->assertMatches('~^[a-f0-9]+$~', Suid::generateHexes(10));
    }

    function testGenerateDigits() {
        $this->assertLength(10, Suid::generateDigits(10));
        $this->assertMatches('~^[0-9]+$~', Suid::generateDigits(10));
    }
}
