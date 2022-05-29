<?php declare(strict_types=1);
namespace froq\test\encoding;
use froq\encoding\encoder\{Encoder, EncoderException, GZipEncoder, ZLibEncoder, JsonEncoder, XmlEncoder};
use froq\encoding\decoder\{Decoder, DecoderException, GZipDecoder, ZLibDecoder, JsonDecoder, XmlDecoder};

class EncoderDecoderTest extends \PHPUnit\Framework\TestCase
{
    function test_options() {
        $encoder = new GZipEncoder(['level' => 9]);

        $this->assertSame(-1, $encoder::getDefaultOptions()['level']);
        $this->assertSame(9, $encoder->getOption('level'));
        $this->assertTrue($encoder->hasOption('level'));
        $this->assertNull($encoder->getOption('absent'));

        $decoder = new GZipDecoder(['length' => 1024]);

        $this->assertSame(0, $decoder::getDefaultOptions()['length']);
        $this->assertSame(1024, $decoder->getOption('length'));
        $this->assertTrue($decoder->hasOption('length'));
        $this->assertNull($decoder->getOption('absent'));


    }

    function test_inputExceptions() {
        try {
            $encoder = new GZipEncoder();
            $encoder->encode();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(EncoderException::class, $e);
            $this->assertRegExp('~No input given yet, call.+setInput()~i', $e->getMessage());
        }

        try {
            $decoder = new GZipDecoder();
            $decoder->decode();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(DecoderException::class, $e);
            $this->assertRegExp('~No input given yet, call.+setInput()~i', $e->getMessage());
        }
    }

    function test_gzip() {
        $decoded = 'Hello!';
        $encoded = '1f8b0800000000000003f348cdc9c957040056cc2a9d06000000';

        $encoder = new GZipEncoder();
        $encoder->setInput($decoded)->encode($error);

        $this->assertSame($encoded, bin2hex($bin = $encoder->getOutput()));
        $this->assertNull($error);

        $decoder = new GZipDecoder();
        $decoder->setInput($bin)->decode($error);

        $this->assertSame($decoded, $decoder->getOutput());
        $this->assertNull($error);
    }

    function test_zlib() {
        $decoded = 'Hello!';
        $encoded = '789cf348cdc9c957040007a20216';

        $encoder = new ZLibEncoder();
        $encoder->setInput($decoded)->encode($error);

        $this->assertSame($encoded, bin2hex($bin = $encoder->getOutput()));
        $this->assertNull($error);

        $decoder = new ZLibDecoder();
        $decoder->setInput($bin)->decode($error);

        $this->assertSame($decoded, $decoder->getOutput());
        $this->assertNull($error);
    }

    function test_json() {
        $decoded = (object) [
            'message' => 'Hello!',
            'date' => '2022-05-29'
        ];
        $encoded = '{"message":"Hello!","date":"2022-05-29"}';

        $encoder = new JsonEncoder();
        $encoder->setInput($decoded)->encode($error);

        $this->assertSame($encoded, $encoder->getOutput());
        $this->assertNull($error);

        $decoder = new JsonDecoder();
        $decoder->setInput($encoded)->decode($error);

        $this->assertEquals($decoded, $decoder->getOutput());
        $this->assertNull($error);
    }

    function test_xml() {
        $decoded = [
            '@root' => [
                'test',
                '@nodes' => [
                    ['message', 'Hello!'],
                    ['date', '2022-05-29'],
                ]
            ]
        ];
        $encoded = '<?xml version="1.0" encoding="utf-8"?><test><message>Hello!</message><date>2022-05-29</date></test>';

        $encoder = new XmlEncoder();
        $encoder->setInput($decoded)->encode($error);

        $this->assertSame($encoded, $encoder->getOutput());
        $this->assertNull($error);

        $decoder = new XmlDecoder();
        $decoder->setInput($encoded)->decode($error);

        $this->assertSame(
            $decoded['@root']['@nodes'][0][1],
            $decoder->getOutput()['test']['message']
        );
        $this->assertNull($error);
    }
}
