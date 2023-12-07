<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Token, TokenException, HashException};

class TokenTest extends \TestCase
{
    function testGenerate() {
        $this->assertLength(Token::LENGTH, Token::generate());
        $this->assertLength(32, $token = Token::generate(32));
        $this->assertTrue(ctype_xdigit($token));

        try {
            Token::generate(10);
        } catch (TokenException $e) {
            $this->assertStringContains('Invalid length', $e->getMessage());
            $this->assertInstanceOf(HashException::class, $e->getCause());
        }
    }
}
