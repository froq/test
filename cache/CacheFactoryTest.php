<?php declare(strict_types=1);
namespace test\froq\cache;
use froq\cache\{Cache, CacheFactory, CacheAgent, CacheException, agent};

class CacheFactoryTest extends \TestCase
{
    function testOptions() {
        try {
            CacheFactory::init('', []);
        } catch (CacheException $e) {
            $this->assertStringContains('No agent id given', $e->getMessage());
        }

        try {
            CacheFactory::init('test', ['agent' => null]);
        } catch (CacheException $e) {
            $this->assertStringContains('Option "agent" is empty', $e->getMessage());
        }

        try {
            CacheFactory::init('test', ['agent' => 'invalid']);
        } catch (CacheException $e) {
            $this->assertStringContains('Unimplemented agent \'invalid\'', $e->getMessage());
        }
    }

    function testInit() {
        $cache = CacheFactory::init('test', $this->options());

        $this->assertInstanceOf(Cache::class, $cache);
    }

    function testInitAgent() {
        $agent = CacheFactory::initAgent('test', $this->options());

        $this->assertInstanceOf(agent\File::class, $agent);
        $this->assertInstanceOf(agent\AgentInterface::class, $agent);
    }

    function testGetInstance() {
        $this->assertNull(CacheFactory::getInstance('none', throw: false));

        $this->expectException(CacheException::class);
        CacheFactory::getInstance('none');
    }

    function testGetAgentInstance() {
        $this->assertNull(CacheFactory::getAgentInstance('none', throw: false));

        $this->expectException(CacheException::class);
        CacheFactory::getAgentInstance('none');
    }

    private function options() {
        return [
            'agent' => CacheAgent::FILE,
            'directory' => tmp() . '/froq-cache',
        ];
    }
}
