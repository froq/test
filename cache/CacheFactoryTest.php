<?php declare(strict_types=1);
namespace test\froq\cache;
use froq\cache\{Cache, CacheFactory, CacheException};
use froq\cache\agent\{AgentInterface, File};

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

        $this->assertInstanceOf(File::class, $agent);
        $this->assertInstanceOf(AgentInterface::class, $agent);
    }

    function testGetInstance() {
        $this->assertNull(CacheFactory::getInstance('none', silent: true));

        $this->expectException(CacheException::class);
        CacheFactory::getInstance('none');
    }

    function testGetAgentInstance() {
        $this->assertNull(CacheFactory::getAgentInstance('none', silent: true));

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
