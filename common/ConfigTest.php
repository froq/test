<?php declare(strict_types=1);
namespace froq\test\common;
use froq\common\object\{Config, ConfigException};

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    function test_updateMethod() {
        $con = new Config(['x' => 1]);

        $this->assertSame(1, $con->get('x'));

        $con->update(['x' => 2, 'y' => ['z' => 22]]);

        $this->assertSame(2, $con->get('x'));
        $this->assertSame(22, $con->get('y.z'));
    }

    function test_getByPathMethod() {
        $con = new Config(['x' => ['y' => ['z' => 1]]]);

        $this->assertSame(1, $con->get('x.y.z'));
    }

    function test_parseDotenv() {
        $file = sprintf('/tmp/test-%s.env', uuid());

        @unlink($file);

        try {
            Config::parseDotenv($file);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConfigException::class, $e);
            $this->assertStringContainsString('No .env file', $e->getMessage());
        }

        file_put_contents($file, <<<ENV
        FOO
        ENV);

        try {
            Config::parseDotenv($file);
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Invalid .env entry', $e->getMessage());
        }

        file_put_contents($file, <<<ENV
        FOO = 1
        FOO = 11
        ENV);

        try {
            Config::parseDotenv($file);
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Duplicated .env entry', $e->getMessage());
        }

        file_put_contents($file, <<<ENV
        FOO = 1
        BAR = 2
        TAR = ["x", "y", "z"]
        ENV);

        $data = Config::parseDotenv($file);
        $this->assertSame('1', $data['FOO']);
        $this->assertSame('2', $data['BAR']);
        $this->assertSame(['x', 'y', 'z'], json_decode($data['TAR']));

        @unlink($file);
    }
}
