<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Hash, HashException};

class HashTest extends \TestCase
{
    function testMake() {
        $input = 'Hello!';

        $this->assertLength(8, Hash::make($input, 8));
        $this->assertLength(32, Hash::make($input, 32));
        $this->assertSame('aa21c9de', Hash::make($input, 8));

        $this->expectException(HashException::class);
        $this->expectExceptionMessageMatches("~Invalid length '1'~");

        Hash::make($input, 1);
    }

    function testMakeBy() {
        $input = 'Hello!';

        $this->assertLength(8, Hash::makeBy($input, 'fnv1a32'));
        $this->assertLength(32, Hash::makeBy($input, 'md5'));
        $this->assertSame('aa21c9de', Hash::makeBy($input, 'fnv1a32'));

        $this->expectException(HashException::class);
        $this->expectExceptionMessageMatches("~Invalid algo 'foo'~");

        Hash::makeBy($input, 'foo');
    }
}
