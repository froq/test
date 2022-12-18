<?php declare(strict_types=1);
namespace test\froq\encrypting\oneway;
use froq\encrypting\oneway\{Sodium, OnewayException};

class SodiumTest extends \TestCase
{
    function testHash() {
        [$pass, $hash] = $this->secrets();
        $pwo = new Sodium();

        $this->assertLength(strlen($hash), $pwo->hash($pass));
    }

    function testVerify() {
        [$pass, $hash] = $this->secrets();
        $pwo = new Sodium();

        $this->assertTrue($pwo->verify($pass, $hash));
        $this->assertFalse($pwo->verify($pass, 'invalid'));
    }

    function testOptions() {
        $pwo = new Sodium();

        $this->assertSame(Sodium::OPS_LIMIT, $pwo->getOption('opslimit'));
        $this->assertSame(Sodium::MEM_LIMIT, $pwo->getOption('memlimit'));

        $pwo = new Sodium(['opslimit' => 2, 'memlimit' => 1024 * 10]);

        $this->assertSame(2, $pwo->getOption('opslimit'));
        $this->assertSame(1024 * 10, $pwo->getOption('memlimit'));

        try {
            new Sodium(['opslimit' => 0]);
        } catch (OnewayException $e) {
            $this->assertEquals('Option "opslimit" is too low, minimum value is 1',
                $e->getMessage());
        }

        try {
            new Sodium(['memlimit' => 1]);
        } catch (OnewayException $e) {
            $this->assertEquals('Option "memlimit" is too low, minimum value is 8KB (8192 bytes)',
                $e->getMessage());
        }
    }

    private function secrets() {
        return [
            'th3-passW0rd!',
            '$argon2id$v=19$m=1024,t=1,p=1$KMHTlS/yRZOfCpgWzccciw$eGGlgRtKolPGZ8t5k1OWllUi33YUIUoFS1UXx+ae48Q'
        ];
    }
}
