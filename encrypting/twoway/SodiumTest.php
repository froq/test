<?php declare(strict_types=1);
namespace test\froq\encrypting\twoway;
use froq\encrypting\twoway\{Sodium, TwowayException};

class SodiumTest extends \TestCase
{
    function setUp(): void {
        if (!extension_loaded('sodium')) {
            $this->markTestSkipped('Sodium extension not loaded, skipped test.');
        }
    }

    function test_constructor() {
        try {
            new Sodium('', '');
        } catch (TwowayException $e) {
            $this->assertStringStartsWith('Invalid key length', $e->getMessage());
        }

        try {
            new Sodium(str_repeat('a', 32), '');
        } catch (TwowayException $e) {
            $this->assertStringStartsWith('Invalid nonce length', $e->getMessage());
        }
    }

    function test_encrypt() {
        [$key, $nonce] = $this->secrets();
        $cod = new Sodium($key, $nonce);

        $this->assertSame('fHYs3D4IEYIh/dDhwOUBmLlXytf6Rw==', $cod->encrypt('Hello!'));

        $cod = new Sodium($key, $nonce, options: ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertSame('7c762cdc3e08118221fdd0e1c0e50198b957cad7fa47', $cod->encrypt('Hello!'));
    }

    function test_decrypt() {
        [$key, $nonce] = $this->secrets();
        $cod = new Sodium($key, $nonce);

        $this->assertSame('Hello!', $cod->decrypt('fHYs3D4IEYIh/dDhwOUBmLlXytf6Rw=='));

        $cod = new Sodium($key, $nonce, options: ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertSame('Hello!', $cod->decrypt('7c762cdc3e08118221fdd0e1c0e50198b957cad7fa47'));
    }

    private function secrets() {
        return [
            'wCVgRBB33PdH3HXZ9yAGuySHIaL0vohE',
            'QJyEv4qlpuYRB5UzJDBSqQOM'
        ];
    }
}
