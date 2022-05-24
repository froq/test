<?php
namespace froq\cache\test;
use froq\cache\{Cache, CacheFactory, CacheException};
use froq\cache\agent\{AgentInterface, File, Apcu, Redis, Memcached};

class CacheFactoryTest extends \PHPUnit\Framework\TestCase
{
    const DIRECTORY = '/tmp/froq-cache';
    const OPTIONS = [
        'agent' => CacheFactory::AGENT_FILE,
        'directory' => self::DIRECTORY,
    ];

    function test_exceptionByEmptyOptions() {
        try {
            CacheFactory::init('', []);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CacheException::class, $e);
            $this->assertStringContainsString('options', $e->getMessage());
        }
    }

    function test_exceptionByEmptyAgentOption() {
        try {
            CacheFactory::init('test', ['agent' => null]);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CacheException::class, $e);
            $this->assertStringContainsString('agent', $e->getMessage());
        }
    }

    function test_cache() {
        $cache = CacheFactory::init('test', self::OPTIONS);

        $this->assertInstanceOf(Cache::class, $cache);
    }

    function test_cacheAgent() {
        $agent = CacheFactory::initAgent('test', self::OPTIONS);

        $this->assertInstanceOf(AgentInterface::class, $agent);
        $this->assertInstanceOf(File::class, $agent);
    }

    function test_absentCacheInstanceException() {
        $this->expectException(CacheException::class);

        CacheFactory::getInstance('none');
    }

    function test_absentCacheAgentInstanceException() {
        $this->expectException(CacheException::class);

        CacheFactory::getAgentInstance('none');
    }
}
