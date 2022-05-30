<?php declare(strict_types=1);
namespace froq\test\cache;
use froq\cache\{Cache, CacheFactory, CacheException};
use froq\cache\agent\{AgentInterface, File, Apcu, Redis, Memcached};

class CacheFactoryTest extends \PHPUnit\Framework\TestCase
{
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
        $cache = CacheFactory::init('test', $this->options());

        $this->assertInstanceOf(Cache::class, $cache);
    }

    function test_cacheAgent() {
        $agent = CacheFactory::initAgent('test', $this->options());

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

    private function options() {
        return [
            'agent' => CacheFactory::AGENT_FILE,
            'directory' => tmp() . '/froq-cache',
        ];
    }
}
