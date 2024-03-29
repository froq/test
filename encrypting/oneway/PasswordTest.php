<?php declare(strict_types=1);
namespace test\froq\encrypting\oneway;
use froq\encrypting\oneway\Password;

class PasswordTest extends \TestCase
{
    function testHash() {
        [$pass, $hash] = $this->secrets();
        $pwo = new Password();

        $this->assertLength(strlen($hash), $pwo->hash($pass));
    }

    function testVerify() {
        [$pass, $hash] = $this->secrets();
        $pwo = new Password();

        $this->assertTrue($pwo->verify($pass, $hash));
        $this->assertFalse($pwo->verify($pass, 'invalid'));
    }

    function testOptions() {
        $pwo = new Password();

        $this->assertSame(PASSWORD_DEFAULT, $pwo->getOption('algo'));
        $this->assertSame(Password::ALGO, $pwo->getOption('algo'));
        $this->assertSame(Password::COST, $pwo->getOption('cost'));

        $pwo = new Password(['algo' => PASSWORD_BCRYPT, 'cost' => 3]);

        $this->assertSame(PASSWORD_BCRYPT, $pwo->getOption('algo'));
        $this->assertSame(3, $pwo->getOption('cost'));
    }

    private function secrets() {
        return [
            'th3-passW0rd!',
            '$2y$09$lqYTTyxdUecX9tpZQGlR8uU1Hp6vpRUyeUlWUwG0NtePqWQALknpe'
        ];
    }
}
