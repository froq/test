<?php declare(strict_types=1);
namespace test\froq\session;
use froq\session\{Session, SessionException};

class SessionTest extends \TestCase
{
    function test_defaultOptions() {
        $session =@ new Session();
        $this->assertSame('SID', $session->option('name'));
        $this->assertFalse($session->option('hash'));
        $this->assertSame(32, $session->option('hashLength'));
        $this->assertFalse($session->option('hashUpper'));
        $this->assertNull($session->option('savePath'));
        $this->assertNull($session->option('saveHandler'));
        $this->assertSame(0, $session->option('cookie.lifetime'));
        $this->assertSame('/', $session->option('cookie.path'));
        $this->assertSame('', $session->option('cookie.domain'));
        $this->assertFalse($session->option('cookie.secure'));
        $this->assertFalse($session->option('cookie.httponly'));
        $this->assertSame('', $session->option('cookie.samesite'));
    }

    function test_customOptions() {
        $options = [
            'name' => 'foo',
            'hash' => true, 'hashLength' => 40, 'hashUpper' => true,
            'savePath' => tmp() . '/froq-session',
            'saveHandler' => [
                'foo\bar\SessionHandler',
                __dir__ . '/../.etc/util/session-handler.php'
            ],
            'cookie' => [
                'lifetime' => 30, 'path' => '/', 'domain' => 'foo.tld',
                'secure' => true, 'httponly' => true, 'samesite' => 'lax',
            ]
        ];

        $session =@ new Session($options);
        $this->assertSame($options['name'], $session->option('name'));
        $this->assertTrue($session->option('hash'));
        $this->assertSame($options['hashLength'], $session->option('hashLength'));
        $this->assertTrue($session->option('hashUpper'));
        $this->assertSame($options['savePath'], $session->option('savePath'));
        $this->assertSame($options['saveHandler'], $session->option('saveHandler'));
        $this->assertSame($options['cookie']['lifetime'], $session->option('cookie.lifetime'));
        $this->assertSame($options['cookie']['path'], $session->option('cookie.path'));
        $this->assertSame($options['cookie']['domain'], $session->option('cookie.domain'));
        $this->assertTrue($session->option('cookie.secure'));
        $this->assertTrue($session->option('cookie.httponly'));
        $this->assertSame($options['cookie']['samesite'], $session->option('cookie.samesite'));
    }

    // Skipped all other header related stuff beause of:
    // Headers already sent at vendor/phpunit/phpunit/src/Util/Printer.php:104.
    // function test_start() {
    //     $session =@ new Session($options);
    //     $this->assertTrue($session->start());
    // }
}
