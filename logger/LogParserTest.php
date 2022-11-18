<?php declare(strict_types=1);
namespace test\froq\logger;
use froq\logger\{Logger, LogParser};

class LogParserTest extends \TestCase
{
    function test_parse() {
        $logger = new Logger(['file' => $this->file()]);
        $logger->log('Test log!');

        $parser = new LogParser($logger->getFile());
        $result = $parser->parse();

        $this->assertInstanceOf(\Generator::class, $result);

        while ($result->valid()) {
            $entry = $result->current();

            $this->assertSame('LOG', $entry['type']);
            // $this->assertSame('...', $entry['date']);
            $this->assertSame('-', $entry['ip']);
            $this->assertSame('Test log!', $entry['content']);
            $this->assertNull($entry['thrown']);

            $result->next();
        }

        unlink($logger->getFile());
    }

    private function file() {
        return format('%s/froq-test/%s.log', tmp(), suid());
    }
}
