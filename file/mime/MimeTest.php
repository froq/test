<?php declare(strict_types=1);
namespace test\froq\file\mime;
use froq\file\mime\{Mime, MimeException};

class MimeTest extends \TestCase
{
    function test_getType() {
        $this->assertSame('text/x-php', Mime::getType(__file__));
        $this->assertSame('directory', Mime::getType(__dir__));

        $this->expectException(MimeException::class);
        $this->expectExceptionMessage('No file exists [file: absent-file]');
        Mime::getType('absent-file', errorCheck: true);
    }

    function test_getExtension() {
        $this->assertSame('php', Mime::getExtension(__file__));
        $this->assertNull(Mime::getExtension(__dir__));
    }

    function test_getTypeByExtension() {
        $this->assertSame('application/x-httpd-php', Mime::getTypeByExtension('php'));
        $this->assertNull(Mime::getTypeByExtension('invalid'));
    }

    function test_getExtensionByType() {
        $this->assertSame('php', Mime::getExtensionByType('application/x-httpd-php'));
        $this->assertSame('php', Mime::getExtensionByType('text/x-php'));
        $this->assertNull(Mime::getExtensionByType('invalid'));
    }
}
