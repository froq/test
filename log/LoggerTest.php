<?php declare(strict_types=1);
namespace test\froq\log;
use froq\log\{Logger, LogLevel};

class LoggerTest extends \TestCase
{
    function testDefaultOptions() {
        $logger = new Logger();

        $this->assertSame(LogLevel::ALL, $logger->getOption('level'));
        $this->assertNull($logger->getOption('tag'));
        $this->assertNull($logger->getOption('directory'));
        $this->assertNull($logger->getOption('file'));
        $this->assertNull($logger->getOption('fileName'));
        $this->assertSame('UTC', $logger->getOption('timeZone'));
        $this->assertSame('D, d M Y H:i:s.u P', $logger->getOption('timeFormat'));
        $this->assertFalse($logger->getOption('json'));
        $this->assertFalse($logger->getOption('rotate'));
    }

    function testCustomOptions() {
        $file = $this->file();
        $options = [
            'level'      => LogLevel::ERROR|LogLevel::WARN,
            'tag'        => 'test',
            'directory'  => dirname($file),
            'file'       => $file,
            'fileName'   => filename($file),
            'timeZone'   => '+00:00',
            'timeFormat' => 'Y-m-d H:i:s P',
            'json'       => true,
            'rotate'     => true,
        ];

        $logger = new Logger($options);
        $this->assertSame($options['level'], $logger->getOption('level'));
        $this->assertSame($options['tag'], $logger->getOption('tag'));
        $this->assertSame($options['directory'], $logger->getOption('directory'));
        $this->assertSame($options['file'], $logger->getOption('file'));
        $this->assertSame($options['fileName'], $logger->getOption('fileName'));
        $this->assertSame($options['timeZone'], $logger->getOption('timeZone'));
        $this->assertSame($options['timeFormat'], $logger->getOption('timeFormat'));
        $this->assertTrue($logger->getOption('json'));
        $this->assertTrue($logger->getOption('rotate'));
    }

    function testLevelMethods() {
        $logger = new Logger();

        $this->assertSame(LogLevel::ALL, $logger->getLevel());
        $this->assertSame(LogLevel::INFO, $logger->setLevel(LogLevel::INFO)->getLevel());
    }

    function testOptionMethods() {
        $logger = new Logger();

        $this->assertSame(null, $logger->getOption('tag'));
        $this->assertSame('test', $logger->setOption('tag', 'test')->getOption('tag'));
    }

    function testLog() {
        $logger = new Logger(['file' => $this->file()]);

        $this->assertTrue($logger->log('Test log!'));

        unlink($logger->getFile());
    }

    function testLogError() {
        $logger = new Logger(['file' => $this->file(), 'level' => LogLevel::ERROR]);

        $this->assertTrue($logger->logError('Test log!'));
        $this->assertFalse($logger->logDebug('Test log!'));

        unlink($logger->getFile());
    }

    function testLogWarn() {
        $logger = new Logger(['file' => $this->file(), 'level' => LogLevel::WARN]);

        $this->assertTrue($logger->logWarn('Test log!'));
        $this->assertFalse($logger->logDebug('Test log!'));

        unlink($logger->getFile());
    }

    function testLogInfo() {
        $logger = new Logger(['file' => $this->file(), 'level' => LogLevel::INFO]);

        $this->assertTrue($logger->logInfo('Test log!'));
        $this->assertFalse($logger->logDebug('Test log!'));

        unlink($logger->getFile());
    }

    function testLogDebug() {
        $logger = new Logger(['file' => $this->file(), 'level' => LogLevel::DEBUG]);

        $this->assertTrue($logger->logDebug('Test log!'));
        $this->assertFalse($logger->logInfo('Test log!'));

        unlink($logger->getFile());
    }

    function testMultiLevelLog() {
        $logger = new Logger(['file' => $this->file(), 'level' => LogLevel::ERROR|LogLevel::WARN]);

        $this->assertTrue($logger->logError('Test log!'));
        $this->assertTrue($logger->logWarn('Test log!'));
        $this->assertFalse($logger->logDebug('Test log!'));

        unlink($logger->getFile());
    }

    private function file() {
        return format('%s/froq-test/%s.log', tmp(), suid());
    }
}
