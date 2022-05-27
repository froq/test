<?php declare(strict_types=1);
namespace froq\test\dom;
use froq\dom\{Dom, DomException, Document, XmlDocument, HtmlDocument};

class DomTest extends \PHPUnit\Framework\TestCase
{
    function test_encodeMethod() {
        $doc = Dom::createXmlDocument();
        $this->assertInstanceOf(XmlDocument::class, $doc);

        $doc->setData($this->getMockXmlData());
        $xml = $doc->toString(options: ['indent' => true, 'indentString' => '  ']);
        $this->assertSame($xml, $this->getMockXmlString());

        try {
            $doc->setData([])->toString();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(DomException::class, $e);
            $this->assertStringContainsString('Invalid document data', $e->getMessage());
        }
    }

    function test_decodeMethod() {
        $data = Dom::parseXml($this->getMockXmlString());
        $this->assertIsArray($data);
        $this->assertSame('rss', $data['@xml']['@root']);
        $this->assertSame('1.0', $data['@xml']['version']);
        $this->assertCount(2, $data['rss']['channel']['item']);

        $data = Dom::parseXml($this->getMockXmlString(), options: ['assoc' => false]);
        $this->assertIsObject($data);
        $this->assertCount(2, $data->rss->channel->item);

        try {
            Dom::parseXml('<invalid');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(DomException::class, $e);
            $this->assertStringContainsString('Parse error', $e->getMessage());
        }
    }

    private function getMockXmlData() {
        return [
            '@root' => [
                'rss',
                '@attributes' => ['version' => '2.0'],
                '@nodes' => [
                    [
                        'channel',
                        '@nodes' => [
                            ['title', 'Lorem Site'],
                            ['description', 'Lorem ipsum dolor.'],
                            ['link', 'https://www.lorem.com'],
                            ['item', /* null, */ '@nodes' => [
                                ['title', 'A Lorem Page'],
                                ['description', 'A Lorem page description.'],
                            ]],
                            ['item', /* null, */ '@nodes' => [
                                ['title', 'B Lorem Page'],
                                ['description', 'B Lorem page description.'],
                            ]],
                        ]
                    ]
                ]
            ]
        ];
    }

    private function getMockXmlString() {
        return <<<XML
        <?xml version="1.0" encoding="utf-8"?>
        <rss version="2.0">
          <channel>
            <title>Lorem Site</title>
            <description>Lorem ipsum dolor.</description>
            <link>https://www.lorem.com</link>
            <item>
              <title>A Lorem Page</title>
              <description>A Lorem page description.</description>
            </item>
            <item>
              <title>B Lorem Page</title>
              <description>B Lorem page description.</description>
            </item>
          </channel>
        </rss>
        XML;
    }
}
