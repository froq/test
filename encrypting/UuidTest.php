<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Uuid, UuidException, Base};

class UuidTest extends \TestCase
{
    function testGenerate() {
        $this->assertLength(36, Uuid::generate());
        $this->assertLength(32, Uuid::generate(dashed: false));

        // Version 4 check.
        $this->assertMatches('~[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-.*~', Uuid::generate());
    }

    function testGenerateHash() {
        $this->assertLength(32, Uuid::generateHash());
        $this->assertLength(40, Uuid::generateHash(40));

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('Format option for only 32-length hashes');
        Uuid::generateHash(16, format: true);
    }

    function testGenerateGuid() {
        $this->assertLength(36, Uuid::generateGuid());
        $this->assertLength(32, Uuid::generateGuid(dashed: false));
    }

    function testGenerateGuidHash() {
        $this->assertLength(32, Uuid::generateGuidHash());
        $this->assertLength(40, Uuid::generateGuidHash(40));

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('Format option for only 32-length hashes');
        Uuid::generateGuidHash(16, format: true);
    }

    function testGenerateWithTimestamp() {
        $timePrefix = dechex(time());

        $this->assertStringStartsWith($timePrefix, Uuid::generateWithTimestamp());
    }

    function testGenerateWithTimestampHash() {
        $this->assertLength(32, Uuid::generateWithTimestampHash());
        $this->assertLength(40, Uuid::generateWithTimestampHash(40));
    }

    function testGenerateWithNamespace() {
        $namespace = 'foo';
        $namespacePrefix = substr(md5($namespace), 0, 8);

        $this->assertStringStartsWith($namespacePrefix, Uuid::generateWithNamespace($namespace));
    }

    function testGenerateWithNamespaceHash() {
        $namespace = 'foo';

        $this->assertLength(32, Uuid::generateWithNamespaceHash($namespace, ));
        $this->assertLength(40, Uuid::generateWithNamespaceHash($namespace, 40));

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('Format option for only 32-length hashes');
        Uuid::generateWithNamespaceHash($namespace, 16, format: true);
    }

    function testGenerateSerial() {
        $this->assertLength(36, Uuid::generateSerial());
        $this->assertLength(32, Uuid::generateSerial(dashed: false));

        $now = datetime('', 'UTC');
        $date = $now->format('Ymd');
        $dateHex = Base::toBase(str_split($now->format('YmdHisu'), 10)[0], 16);

        $this->assertStringStartsWith($date, Uuid::generateSerial(dated: true));
        $this->assertStringStartsWith($dateHex, Uuid::generateSerial(dated: true, hexed: true));

        $this->assertMatches('~^[0-9]{8}-[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{12}$~',
            Uuid::generateSerial());
        $this->assertMatches('~^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$~',
            Uuid::generateSerial(hexed: true));
    }

    function testGenerateRandomSerial() {
        $this->assertLength(36, Uuid::generateRandomSerial());
        $this->assertLength(32, Uuid::generateRandomSerial(dashed: false));

        $this->assertMatches('~^[0-9]{8}-[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{12}$~',
            Uuid::generateRandomSerial());
        $this->assertMatches('~^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$~',
            Uuid::generateRandomSerial(hexed: true));
    }

    function testIsValid() {
        $uuid = 'a0551dd1-eda1-4a8b-905a-a51917723c53';
        $guid = '40f4f461-253e-6f83-ac0e-4f75becc416d';

        $this->assertTrue(Uuid::isValid($uuid));
        $this->assertFalse(Uuid::isValid($guid));
        $this->assertTrue(Uuid::isValid($guid, strict: false));
    }

    function testIsValidHash() {
        $hash = 'b172f392279e24575fa05517057ade35';

        $this->assertTrue(Uuid::isValidHash($hash));
        $this->assertFalse(Uuid::isValidHash('invalid'));
    }

    function testFormat() {
        $plainHex = 'b50c8b4cabd12ed85ad4fec7d3ef3789';
        $formatted = 'b50c8b4c-abd1-2ed8-5ad4-fec7d3ef3789';

        $this->assertSame($formatted, Uuid::format($plainHex));

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('Input must be a 32-length x-digit');
        Uuid::format('invalid');
    }

    function testFormatBinary() {
        $binary = random_bytes(16);

        $this->assertLength(36, Uuid::formatBinary($binary));
        $this->assertLength(32, Uuid::formatBinary($binary, dashed: false));

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('Input must be a 32-length x-digit');
        Uuid::formatBinary(str_repeat(' ', 32));
    }
}
