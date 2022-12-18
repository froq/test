<?php declare(strict_types=1);
namespace test\froq\file\mime;
use froq\file\mime\Mime;

class MimeTest extends \TestCase
{
    function testGetType() {
        $this->assertSame('directory', Mime::getType(__DIR__));
        $this->assertSame('text/x-php', Mime::getType(__FILE__));
    }

    function testGetTypeByExtension() {
        $this->assertSame('application/x-httpd-php', Mime::getTypeByExtension('php'));
        $this->assertNull(Mime::getTypeByExtension('invalid'));
    }

    function testGetExtensionByType() {
        $this->assertSame('php', Mime::getExtensionByType('application/x-httpd-php'));
        $this->assertSame('php', Mime::getExtensionByType('text/x-php'));
        $this->assertNull(Mime::getExtensionByType('invalid'));
    }
}
