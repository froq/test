<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\Reflection;

class ReflectionTest extends \TestCase
{
    function test_getVisibility() {
        $this->assertSame('public', Reflection::getVisibility($this->reflect('publicMethod')));
        $this->assertSame('private', Reflection::getVisibility($this->reflect('privateMethod')));
        $this->assertSame('protected', Reflection::getVisibility($this->reflect('protectedMethod')));
    }

    public function publicMethod() {}
    private function privateMethod() {}
    protected function protectedMethod() {}

    private function reflect($method) {
        return new \ReflectionMethod($this, $method);
    }
}
