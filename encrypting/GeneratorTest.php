<?php declare(strict_types=1);
namespace froq\test\encrypting;
use froq\encrypting\{Generator, GeneratorException, HashException};

class GeneratorTest extends \TestCase
{
    function test_generateSalt() {
        $this->assertLength(40, Generator::generateSalt());
        $this->assertLength(10, Generator::generateSalt(10));
    }

    function test_generateNonce() {
        $this->assertLength(16, Generator::generateNonce());
        $this->assertLength(20, Generator::generateNonce(20));
    }

    function test_generateToken() {
        $this->assertLength(32, Generator::generateToken());
        $this->assertLength(40, Generator::generateToken(40));

        try {
            Generator::generateToken(10);
        } catch (GeneratorException $e) {
            $this->assertInstanceOf(HashException::class, $e->getCause());
            $this->assertStringContains('Invalid length', $e->getMessage());
        }
    }

    function test_generateUuid() {
        $this->assertLength(36, Generator::generateUuid());
        $this->assertLength(32, Generator::generateUuid(dashed: false));
    }

    function test_generateGuid() {
        $this->assertLength(36, Generator::generateGuid());
        $this->assertLength(32, Generator::generateGuid(dashed: false));
    }

    function test_generateSerial() {
        $this->assertLength(20, Generator::generateSerial());
        $this->assertLength(30, Generator::generateSerial(30));

        $time = date('U'); $date = date('Ymd');

        $this->assertStringStartsWith($time, Generator::generateSerial(dated: false));
        $this->assertStringStartsNotWith($time, Generator::generateSerial(dated: true));

        $this->assertStringStartsWith($date, Generator::generateSerial(dated: true));
        $this->assertStringStartsNotWith($date, Generator::generateSerial(dated: false));

        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('Argument $length must be minimun 20, 1 given');
        Generator::generateRandomSerial(1);
    }

    function test_generateRandomSerial() {
        $this->assertLength(20, Generator::generateRandomSerial());
        $this->assertLength(30, Generator::generateRandomSerial(30));

        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('Argument $length must be minimun 20, 1 given');
        Generator::generateRandomSerial(1);
    }

    function test_generateId() {
        $this->assertLength(10, Generator::generateId(10));
        $this->assertMatches('~^[a-zA-Z0-9]+$~', Generator::generateId(10));
        $this->assertMatches('~^[a-z0-9]+$~', Generator::generateId(10, base: 36));
        $this->assertMatches('~^[a-f0-9]+$~', Generator::generateId(10, base: 16));

        $time = date('U'); $date = date('Ymd');

        $this->assertStringStartsWith($time, Generator::generateId(10, dated: false));
        $this->assertStringStartsNotWith($time, Generator::generateId(10, dated: true));

        $this->assertStringStartsWith($date, Generator::generateId(10, dated: true));
        $this->assertStringStartsNotWith($date, Generator::generateId(10, dated: false));

        try { // Let next error.
            Generator::generateId(1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $length must be minimun 10, 1 given', $e->getMessage());
        }

        try {
            Generator::generateId(10, base: 1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $base must be between 10-62, 1 given', $e->getMessage());
        }
    }

    function test_generateShortId() {
        $this->assertLength(16, Generator::generateShortId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateShortId(dated: true));
        $this->assertStringMatchesFormat('%x', Generator::generateShortId(base: 16));
    }

    function test_generateLongId() {
        $this->assertLength(32, Generator::generateLongId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateLongId(dated: true));
        $this->assertStringMatchesFormat('%x', Generator::generateLongId(base: 16));
    }

    function test_generateSerialId() {
        $this->assertLength(20, Generator::generateSerialId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateSerialId(true));
    }

    function test_generateRandomId() {
        $this->assertLength(10, Generator::generateRandomId(10));

        try { // Let next error.
            Generator::generateRandomId(1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $length must be minimun 4, 1 given', $e->getMessage());
        }

        try {
            Generator::generateRandomId(10, base: 1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $base must be between 10-62, 1 given', $e->getMessage());
        }
    }

    function test_generateSessionId() {
        $this->assertLength(26, Generator::generateSessionId());
        $this->assertLength(32, Generator::generateSessionId(['hash' => true, 'hashLength' => 32]));
    }

    function test_generateObjectId() {
        $this->assertLength(24, Generator::generateObjectId());
    }

    function test_generatePassword() {
        $this->assertLength(8, Generator::generatePassword());
        $this->assertLength(12, Generator::generatePassword(12));
    }

    function test_generateOneTimePassword() {
        $this->assertLength(6, Generator::generateOneTimePassword('secret'));
        $this->assertLength(10, Generator::generateOneTimePassword('secret', 10));
    }
}
