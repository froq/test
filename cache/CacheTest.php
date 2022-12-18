<?php declare(strict_types=1);
namespace test\froq\cache;
use froq\cache\{Cache, CacheFactory, CacheException};
use froq\cache\agent\{AgentInterface, File};

class CacheTest extends \TestCase
{
    function testOptions() {
        try {
            new Cache('test', []);
        } catch (CacheException $e) {
            $this->assertStringContains('No agent options given', $e->getMessage());
        }

        try {
            new Cache('test', ['agent' => null]);
        } catch (CacheException $e) {
            $this->assertStringContains('Option "agent" is empty', $e->getMessage());
        }

        try {
            new Cache('test', ['agent' => 'invalid']);
        } catch (CacheException $e) {
            $this->assertStringContains('Unimplemented agent \'invalid\'', $e->getMessage());
        }
    }

    function testFileAgent() {
        $cache = new Cache('test', $this->options());

        $this->assertInstanceOf(File::class, $cache->agent);
        $this->assertInstanceOf(AgentInterface::class, $cache->agent);

        $this->assertSame($this->options()['directory'], $cache->agent->getDirectory());
        $this->assertDirectoryExists($cache->agent->getDirectory());
    }

    function testFileAgentStorage() {
        $cache = new Cache('test', $this->options());
        $key = 'foo'; $value = 123;

        $this->assertFalse($cache->agent->has($key));

        $cache->agent->set($key, $value);
        $this->assertTrue($cache->agent->has($key));
        $this->assertSame($value, $cache->agent->get($key));

        $cache->agent->delete($key);
        $this->assertFalse($cache->agent->has($key));
        $this->assertNull($cache->agent->get($key));

        $cache->clear();
    }

    function testCacheStorage() {
        $cache = new Cache('test', $this->options());
        $key = 'foo'; $value = 123;

        $this->assertFalse($cache->has($key));

        $cache->set($key, $value);
        $this->assertTrue($cache->has($key));
        $this->assertSame($value, $cache->get($key));

        $cache->delete($key);
        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));

        $cache->clear();
    }

    function testCacheMultiStorage() {
        $cache = new Cache('test', $this->options());
        $items = ['foo' => 123, 'bar' => true];
        $keys = array_keys($items);
        $values = array_values($items);

        $this->assertFalse($cache->has($keys));

        $cache->set($items);
        $this->assertTrue($cache->has($keys));
        $this->assertSame($values, $cache->get($keys));

        $cache->delete($keys);
        $this->assertFalse($cache->has($keys));
        $this->assertSame([null, null], $cache->get($keys));

        $cache->clear();
    }

    private function options() {
        return [
            'agent' => CacheFactory::AGENT_FILE,
            'directory' => tmp() . '/froq-cache',
        ];
    }
}
