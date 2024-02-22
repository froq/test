<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Csrf, CsrfException};

class CsrfTest extends \TestCase
{
    function testSettersGetters() {
        $csrf = new Csrf();
        $this->assertNull($csrf->getToken());

        $csrf->setToken(Csrf::generateToken());
        $this->assertNotNull($csrf->getToken());

        $csrf = new Csrf(Csrf::generateToken());
        $this->assertNotNull($csrf->getToken());
    }

    function testValidators() {
        $csrf = new Csrf($token = Csrf::generateToken());
        $this->assertTrue($csrf->validate($token)); // Alias.
        $this->assertTrue($csrf->validateToken($token));

        $this->assertFalse($csrf->validate('invalid-token')); // Alias.
        $this->assertFalse($csrf->validateToken('invalid-token'));

        $this->assertTrue(Csrf::validateTokens($csrf->getToken(), $token));
        $this->assertFalse(Csrf::validateTokens($csrf->getToken(), 'invalid-token'));

        $this->expectException(CsrfException::class);
        $this->expectExceptionMessage('No token given yet, set token before validation');
        $csrf = new Csrf(); $csrf->validate('');
    }

    function testGenerateToken() {
        $token = Csrf::generateToken();
        $this->assertLength(40, $token);
        $this->assertTrue(ctype_xdigit($token));
    }
}
