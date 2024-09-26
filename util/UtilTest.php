<?php declare(strict_types=1);
namespace test\froq\util;
use froq\util\{Util, UtilException};

class UtilTest extends \TestCase
{
    function testLoadSugar() {
        $this->assertFunctionNotExists('html_encode');

        Util::loadSugar('html');

        $this->assertFunctionExists('html_encode');

        $this->expectException(UtilException::class);
        $this->expectExceptionMessageMatches("~^Invalid sugar name 'foo'~");

        Util::loadSugar('foo');
    }

    function testGetClientIp() {
        $_SERVER['REMOTE_ADDR'] = $ip = '1.1.1.1'; // Fake it.

        $this->assertSame($ip, Util::getClientIp());
    }

    function testGetClientUa() {
        $_SERVER['HTTP_USER_AGENT'] = $ua = 'Test UA'; // Fake it.

        $this->assertSame($ua, Util::getClientUa());
    }

    function testGetCurrentUrl() {
        $_SERVER['REQUEST_SCHEME'] = $scheme = 'http';
        $_SERVER['REQUEST_URI']    = $uri    = '/a/1';
        $_SERVER['SERVER_NAME']    = $host   = 'local';
        $_SERVER['SERVER_PORT']    = $port   = 8080;

        $url = sprintf('%s://%s:%s%s', $scheme, $host, $port, $uri);

        $this->assertSame($url, Util::getCurrentUrl());
    }

    function testFormatBytes() {
        $bytes = 1024 ** 2; // 1MB/1024KB

        $this->assertSame('1024KB', Util::formatBytes($bytes, 0));
    }

    function testConvertBytes() {
        $bytes = '1MB'; // 1MB/1024KB

        $this->assertSame(1048576, Util::convertBytes($bytes));
    }

    function testMakeClosure() {
        $closure = Util::makeClosure(fn() => true, bind: null);

        $this->assertInstanceOf(\Closure::class, $closure);
    }

    function testMakeArray() {
        $array = ['a' => 1, 'b' => ['c' => []]];
        $object = (object) $array;
        $arrayObject = new \ArrayObject($array);

        $this->assertEquals($array, Util::makeArray($array));
        $this->assertEquals($array, Util::makeArray($object));
        $this->assertEquals($array, Util::makeArray($arrayObject));
    }

    function testMakeObject() {
        $object = (object) ['a' => 1, 'b' => ['c' => []]];
        $array = (array) $object;
        $arrayObject = new \ArrayObject($array);

        $this->assertEquals($object, Util::makeObject($object));
        $this->assertEquals($object, Util::makeObject($array));
        $this->assertEquals($object, Util::makeObject($arrayObject));
    }
}
