<?php declare(strict_types=1);
namespace froq\test\encrypting;
use froq\encrypting\Base62;

class Base62Test extends \TestCase
{
    function test_encode() {
        $this->assertSame('mBps3ubT', Base62::encode('Hello!'));
        $this->assertSame('l18lRFrpbZ9y2361', Base62::encode('Hello!', bin: true));
    }

    function test_decode() {
        $this->assertSame('Hello!', Base62::decode('mBps3ubT'));
        $this->assertSame('Hello!', Base62::decode('l18lRFrpbZ9y2361', bin: true));
    }
}
