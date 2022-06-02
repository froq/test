<?php declare(strict_types=1);
namespace froq\test\encoding;
use froq\encoding\codec\{Codec, GZipCodec, ZLibCodec, JsonCodec, XmlCodec};
use froq\encoding\encoder\{GZipEncoder, ZLibEncoder, JsonEncoder, XmlEncoder};
use froq\encoding\decoder\{GZipDecoder, ZLibDecoder, JsonDecoder, XmlDecoder};

class CodecTest extends \TestCase
{
    function test_properties() {
        $codec = new GZipCodec();

        try {
            $codec->absentProperty;
        } catch (\UndefinedPropertyError $e) {
            $this->assertMatches('~Undefined property.+\$absentProperty~i', $e->getMessage());
        }

        try {
            $codec->absentProperty = 123;
        } catch (\UndefinedPropertyError $e) {
            $this->assertMatches('~Undefined property.+\$absentProperty~i', $e->getMessage());
        }

        // Creates on demand via __get() for once.
        $this->assertInstanceOf(GZipEncoder::class, $codec->encoder);
        $this->assertInstanceOf(GZipDecoder::class, $codec->decoder);

        try {
            $codec->encoder = 123;
        } catch (\ReadonlyPropertyError $e) {
            $this->assertMatches('~Cannot modify readonly property.+\$encoder~', $e->getMessage());
        }

        try {
            $codec->decoder = 123;
        } catch (\ReadonlyPropertyError $e) {
            $this->assertMatches('~Cannot modify readonly property.+\$decoder~', $e->getMessage());
        }
    }

    function test_options() {
        $codec = new JsonCodec([
            'encoder' => ['indent' => 2],
            'decoder' => ['assoc' => true],
        ]);

        $this->assertSame(2, $codec->encoder->getOption('indent'));
        $this->assertSame(true, $codec->decoder->getOption('assoc'));
    }

    function test_gzip() {
        $codec = new GZipCodec();
        $data = 'Hello!';
        $magic = "\x1F\x8B"; // Constant.

        $this->assertSame(
            '1f8b0800000000000003f348cdc9c957040056cc2a9d06000000',
            bin2hex($encoded = $codec->encode($data, $error))
        );
        $this->assertNull($error);
        $this->assertSame($data, $codec->decode($encoded));
        $this->assertTrue(GZipEncoder::isEncoded($encoded));
        $this->assertInstanceOf(GZipEncoder::class, $codec->encoder);
        $this->assertInstanceOf(GZipDecoder::class, $codec->decoder);
        $this->assertStringStartsWith($magic, $encoded);
    }

    function test_zlib() {
        $codec = new ZLibCodec();
        $data = 'Hello!';
        $magic = "\x78\x9C"; // Default.

        $this->assertSame(
            '789cf348cdc9c957040007a20216',
            bin2hex($encoded = $codec->encode($data, $error))
        );
        $this->assertNull($error);
        $this->assertSame($data, $codec->decode($encoded));
        $this->assertTrue(ZLibEncoder::isEncoded($encoded));
        $this->assertInstanceOf(ZLibEncoder::class, $codec->encoder);
        $this->assertInstanceOf(ZLibDecoder::class, $codec->decoder);
        $this->assertStringStartsWith($magic, $encoded);
    }

    function test_json() {
        $codec = new JsonCodec();
        $data = (object) [
            'message' => 'Hello!',
            'date' => '2022-05-29'
        ];

        $this->assertSame(
            '{"message":"Hello!","date":"2022-05-29"}',
            $encoded = $codec->encode($data, $error)
        );
        $this->assertNull($error);
        $this->assertEquals($data, $codec->decode($encoded));
        $this->assertTrue(JsonEncoder::isEncoded($encoded));
        $this->assertInstanceOf(JsonEncoder::class, $codec->encoder);
        $this->assertInstanceOf(JsonDecoder::class, $codec->decoder);
    }

    function test_xml() {
        $codec = new XmlCodec();
        $data = [
            '@root' => [
                'test',
                '@nodes' => [
                    ['message', 'Hello!'],
                    ['date', '2022-05-29'],
                ]
            ]
        ];

        $this->assertSame(
            '<?xml version="1.0" encoding="utf-8"?><test><message>Hello!</message><date>2022-05-29</date></test>',
            $encoded = $codec->encode($data, $error)
        );
        $this->assertNull($error);
        $this->assertSame(
            $data['@root']['@nodes'][0][1],
            $codec->decode($encoded)['test']['message']
        );
        $this->assertTrue(XmlEncoder::isEncoded($encoded));
        $this->assertInstanceOf(XmlEncoder::class, $codec->encoder);
        $this->assertInstanceOf(XmlDecoder::class, $codec->decoder);
    }
}
