<?php
use froq\cache\{Cache, CacheFactory, CacheException};
use froq\cache\agent\{AgentInterface, File, Apcu, Redis, Memcached};

class CacheTest extends PHPUnit\Framework\TestCase
{
    const DIRECTORY = '/tmp/froq-cache';
    const OPTIONS = [
        'agent' => CacheFactory::AGENT_FILE,
        'directory' => self::DIRECTORY,
    ];

    function test_exceptionByEmptyOptions() {
        try {
            new Cache('test', []);
        } catch (Throwable $e) {
            $this->assertInstanceOf(CacheException::class, $e);
            $this->assertStringContainsString('options', $e->getMessage());
        }
    }

    function test_exceptionByEmptyAgentOption() {
        try {
            new Cache('test', ['agent' => null]);
        } catch (Throwable $e) {
            $this->assertInstanceOf(CacheException::class, $e);
            $this->assertStringContainsString('agent', $e->getMessage());
        }
    }

    function test_fileAgent() {
        $cache = new Cache('test', self::OPTIONS);

        $this->assertInstanceOf(AgentInterface::class, $cache->agent);
        $this->assertInstanceOf(File::class, $cache->agent);

        $this->assertEquals(self::DIRECTORY, $cache->agent->getDirectory());
        $this->assertDirectoryExists($cache->agent->getDirectory());
    }

    function test_fileAgentStorage() {
        $cache = new Cache('test', self::OPTIONS);
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

    function test_cacheStorage() {
        $cache = new Cache('test', self::OPTIONS);
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

    function test_cacheMultiStorage() {
        $cache = new Cache('test', self::OPTIONS);
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
}
