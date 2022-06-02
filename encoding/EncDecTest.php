<?php declare(strict_types=1);
namespace froq\test\encoding;
use froq\encoding\encoder\{EncoderError, EncoderException, GZipEncoder, ZLibEncoder, JsonEncoder, XmlEncoder};
use froq\encoding\decoder\{DecoderError, DecoderException, GZipDecoder, ZLibDecoder, JsonDecoder, XmlDecoder};

class EncDecTest extends \TestCase
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

    function test_inputErrors() {
        try {
            $encoder = new GZipEncoder(['throwErrors' => true]);
            $encoder->setInput(null)->encode();
        } catch (EncoderError $e) {
            $this->assertInstanceOf(\TypeError::class, $e->getCause());
            $this->assertStringContains('must be of type string, null given', $e->getMessage());
        }

        try {
            $decoder = new GZipDecoder(['throwErrors' => true]);
            $decoder->setInput(null)->decode();
        } catch (DecoderError $e) {
            $this->assertInstanceOf(\TypeError::class, $e->getCause());
            $this->assertStringContains('must be of type string, null given', $e->getMessage());
        }
    }

    function test_inputExceptions() {
        try {
            $encoder = new GZipEncoder();
            $encoder->encode();
        } catch (EncoderException $e) {
            $this->assertMatches('~No input given yet, call.+setInput()~i', $e->getMessage());
        }

        try {
            $decoder = new GZipDecoder();
            $decoder->decode();
        } catch (DecoderException $e) {
            $this->assertMatches('~No input given yet, call.+setInput()~i', $e->getMessage());
        }
    }

    function test_convert() {
        $decoded = 'Hello!';
        $encoded = '1f8b0800000000000003f348cdc9c957040056cc2a9d06000000';

        $encoder = new GZipEncoder();

        $this->assertSame($encoded, bin2hex($bin = $encoder->convert($decoded, $error)));
        $this->assertNull($error);

        $decoder = new GZipDecoder();

        $this->assertSame($decoded, $decoder->convert($bin, $error));
        $this->assertNull($error);
    }

    function test_gzip() {
        $decoded = 'Hello!';
        $encoded = '1f8b0800000000000003f348cdc9c957040056cc2a9d06000000';

        $encoder = new GZipEncoder();
        $encoder->setInput($decoded);

        $this->assertTrue($encoder->encode());
        $this->assertNull($encoder->error());
        $this->assertSame($encoded, bin2hex($bin = $encoder->getOutput()));

        $decoder = new GZipDecoder();
        $decoder->setInput($bin);

        $this->assertTrue($decoder->decode());
        $this->assertNull($encoder->error());
        $this->assertSame($decoded, $decoder->getOutput());
    }

    function test_zlib() {
        $decoded = 'Hello!';
        $encoded = '789cf348cdc9c957040007a20216';

        $encoder = new ZLibEncoder();
        $encoder->setInput($decoded);

        $this->assertTrue($encoder->encode());
        $this->assertNull($encoder->error());
        $this->assertSame($encoded, bin2hex($bin = $encoder->getOutput()));

        $decoder = new ZLibDecoder();
        $decoder->setInput($bin);

        $this->assertTrue($decoder->decode());
        $this->assertNull($decoder->error());
        $this->assertSame($decoded, $decoder->getOutput());
    }

    function test_json() {
        $decoded = (object) [
            'message' => 'Hello!',
            'date' => '2022-05-29'
        ];
        $encoded = '{"message":"Hello!","date":"2022-05-29"}';

        $encoder = new JsonEncoder();
        $encoder->setInput($decoded);

        $this->assertTrue($encoder->encode());
        $this->assertNull($encoder->error());
        $this->assertSame($encoded, $encoder->getOutput());

        $decoder = new JsonDecoder();
        $decoder->setInput($encoded);

        $this->assertTrue($decoder->decode());
        $this->assertNull($decoder->error());
        $this->assertEquals($decoded, $decoder->getOutput());

        // Custom JSON object.
        $sourceObject = new class { var $message = 'Hello!', $date = '2022-05-29'; };
        $targetObject = new class { var $message, $date; };

        $encoder = new JsonEncoder();

        // No setInput() here, passing object as input.
        $this->assertTrue($encoder->encode($sourceObject));
        $this->assertNull($encoder->error());
        $this->assertSame($encoded, $encoder->getOutput());

        $decoder = new JsonDecoder();
        $decoder->setInput($encoded);

        $this->assertTrue($decoder->decode($targetObject));
        $this->assertNull($decoder->error());
        $this->assertEquals($targetObject, $decoder->getOutput());
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
        $encoded = '<?xml version="1.0" encoding="utf-8"?><test>'
                 . '<message>Hello!</message><date>2022-05-29</date></test>';

        $encoder = new XmlEncoder();
        $encoder->setInput($decoded);

        $this->assertTrue($encoder->encode());
        $this->assertNull($encoder->error());
        $this->assertSame($encoded, $encoder->getOutput());

        $decoder = new XmlDecoder();
        $decoder->setInput($encoded);

        $this->assertTrue($decoder->decode());
        $this->assertNull($decoder->error());
        $this->assertSame(
            $decoded['@root']['@nodes'][0][1],
            $decoder->getOutput()['test']['message']
        );
    }
}
