<?php declare(strict_types=1);
namespace test\froq\validation;
use froq\validation\{Validation, ValidationError};

class ValidationTest extends \TestCase
{
    function test_required() {
        $validation = new Validation(['id' => ['type' => 'int', 'required']]);

        $data = ['id' => 123];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['id' => null];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::REQUIRED, $validation->errors()['id']['code']);
        $this->assertSame("Field 'id' is required, none given.", $validation->errors()['id']['message']);
    }

    function test_errors() {
        $validation = new Validation(['id' => ['type' => 'int', 'required']], options: ['throwErrors' => true]);

        $data = ['id' => 123];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['id' => null];
        try {
            $validation->validate($data);
        } catch (ValidationError $e) {
            $this->assertSame("Validation failed, use errors() to see error details", $e->getMessage());
            $this->assertSame(['id' => ['code' => ValidationError::REQUIRED, 'message' => "Field 'id' is required, none given."]],
                $e->errors());
        }
    }

    function test_arrayValidation() {
        $validation = new Validation(['tags' => ['type' => 'array']]);

        $data = ['tags' => ['foo']];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['tags' => 'foo'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::TYPE, $validation->errors()['tags']['code']);
        $this->assertSame("Field 'tags' value must be array, string given.", $validation->errors()['tags']['message']);
    }

    function test_boolValidation() {
        $validation = new Validation(['is_deleted' => ['type' => 'bool']]);

        $data = ['is_deleted' => true];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['is_deleted' => 'foo'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::TYPE, $validation->errors()['is_deleted']['code']);
        $this->assertSame("Field 'is_deleted' value must be true or false, string given.", $validation->errors()['is_deleted']['message']);
    }

    function test_callbackValidation() {
        $validation = new Validation(['id' => ['type' => 'int', 'spec' => (
            static function ($value, $data, &$error) {
                if ($value < 0) {
                    $error = 'Invalid ID value.';
                    return false; // Must return false.
                }
            }
        )]]);

        $data = ['id' => 123];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['id' => -123];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::CALLBACK, $validation->errors()['id']['code']);
        $this->assertSame("Invalid ID value.", $validation->errors()['id']['message']);
    }

    function test_datetimeValidation() {
        $validation = new Validation(['date' => ['type' => 'date']]);

        $data = ['date' => '2022-01-01'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['date' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['date']['code']);
        $this->assertSame("Field 'date' value is not a valid date.", $validation->errors()['date']['message']);

        $validation = new Validation(['time' => ['type' => 'time']]);

        $data = ['time' => '20:22:01'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['time' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['time']['code']);
        $this->assertSame("Field 'time' value is not a valid time.", $validation->errors()['time']['message']);

        $validation = new Validation(['datetime' => ['type' => 'datetime']]);

        $data = ['datetime' => '2022-01-01 20:22:01'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['datetime' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['datetime']['code']);
        $this->assertSame("Field 'datetime' value is not a valid datetime.", $validation->errors()['datetime']['message']);

        // With specs.
        $validation1 = new Validation(['created_at' => ['type' => 'datetime', 'spec' => 'Y/m/d H:i']]);
        $validation2 = new Validation(['created_at' => ['type' => 'datetime', 'spec' => '~\d{2,4}/\d{2}/\d{2} \d{2}:\d{2}~']]);

        $data = ['created_at' => '2022/01/01 20:22'];
        $this->assertTrue($validation1->validate($data));
        $this->assertNull($validation1->errors());

        $data = ['created_at' => 'invalid'];
        $this->assertFalse($validation1->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation1->errors()['created_at']['code']);
        $this->assertSame("Field 'created_at' value is not a valid datetime.", $validation1->errors()['created_at']['message']);

        $data = ['created_at' => '22/01/01 20:22'];
        $this->assertTrue($validation2->validate($data));
        $this->assertNull($validation2->errors());

        $data = ['created_at' => 'invalid'];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::NOT_MATCH, $validation2->errors()['created_at']['code']);
        $this->assertSame("Field 'created_at' value did not match with given pattern.", $validation2->errors()['created_at']['message']);
    }

    function test_emailValidation() {
        $validation = new Validation(['email' => ['type' => 'email']]);

        $data = ['email' => 'foo@bar.com'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['email' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::EMAIL, $validation->errors()['email']['code']);
        $this->assertSame("Field 'email' value is not a valid email address.", $validation->errors()['email']['message']);
    }

    function test_enumValidation() {
        $validation = new Validation(['types' => ['type' => 'enum', 'spec' => ['foo', 'bar']]]);

        $data = ['types' => 'foo'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['types' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::ENUM, $validation->errors()['types']['code']);
        $this->assertSame("Field 'types' value must be one of these options: foo, bar.", $validation->errors()['types']['message']);
    }

    function test_jsonValidation() {
        $validation = new Validation(['tech' => ['type' => 'json']]);

        $data = ['tech' => '{"os":"linux"}'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['tech' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['tech']['code']);
        $this->assertSame("Field 'tech' value is not a valid JSON input.", $validation->errors()['tech']['message']);

        // With specs.
        $validation1 = new Validation(['tech' => ['type' => 'json', 'spec' => 'array']]);
        $validation2 = new Validation(['tech' => ['type' => 'json', 'spec' => 'object']]);

        $data = ['tech' => '[{"os":"linux"}]'];
        $this->assertTrue($validation1->validate($data));
        $this->assertNull($validation1->errors());

        $data = ['tech' => 'invalid'];
        $this->assertFalse($validation1->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation1->errors()['tech']['code']);
        $this->assertSame("Field 'tech' value is not a valid JSON array.", $validation1->errors()['tech']['message']);

        $data = ['tech' => '{"os":"linux"}'];
        $this->assertTrue($validation2->validate($data));
        $this->assertNull($validation2->errors());

        $data = ['tech' => 'invalid'];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation2->errors()['tech']['code']);
        $this->assertSame("Field 'tech' value is not a valid JSON object.", $validation2->errors()['tech']['message']);
    }

    function test_numberValidation() {
        $validation = new Validation(['no' => ['type' => 'number', /* int, float or numeric. */]]);

        $data = ['no' => 5];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['no' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::TYPE, $validation->errors()['no']['code']);
        $this->assertSame("Field 'no' value must be number, string given.", $validation->errors()['no']['message']);

        // With options.
        $validation1 = new Validation(['no' => ['type' => 'number', 'range' => [1, 10]]]);
        $validation2 = new Validation(['no' => ['type' => 'number', 'min' => 1, 'max' => 10]]);

        $data = ['no' => 123];
        $this->assertFalse($validation1->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation1->errors()['no']['code']);
        $this->assertSame("Field 'no' value must be between 1 and 10.", $validation1->errors()['no']['message']);

        $data = ['no' => 0];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::MIN_VALUE, $validation2->errors()['no']['code']);
        $this->assertSame("Field 'no' value must be minimum 1.", $validation2->errors()['no']['message']);

        $data = ['no' => 11];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::MAX_VALUE, $validation2->errors()['no']['code']);
        $this->assertSame("Field 'no' value must be maximum 10.", $validation2->errors()['no']['message']);
    }

    function test_stringValidation() {
        $validation = new Validation(['name' => ['type' => 'string']]);

        $data = ['name' => 'Kerem'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['name' => 123];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::TYPE, $validation->errors()['name']['code']);
        $this->assertSame("Field 'name' value must be string, int given.", $validation->errors()['name']['message']);

        // With options.
        $validation1 = new Validation(['name' => ['type' => 'string', 'spec' => '~^\w+$~']]);
        $validation2 = new Validation(['name' => ['type' => 'string', 'minlen' => 5, 'maxlen' => 10]]);

        $data = ['name' => 'Kerem..'];
        $this->assertFalse($validation1->validate($data));
        $this->assertSame(ValidationError::NOT_MATCH, $validation1->errors()['name']['code']);
        $this->assertSame("Field 'name' value did not match with given pattern.", $validation1->errors()['name']['message']);

        $data = ['name' => 'Ker'];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::MIN_LENGTH, $validation2->errors()['name']['code']);
        $this->assertSame("Field 'name' value length must be minimum 5.", $validation2->errors()['name']['message']);

        $data = ['name' => 'Kerem Kerem Kerem'];
        $this->assertFalse($validation2->validate($data));
        $this->assertSame(ValidationError::MAX_LENGTH, $validation2->errors()['name']['code']);
        $this->assertSame("Field 'name' value length must be maximum 10.", $validation2->errors()['name']['message']);
    }

    function test_epochValidation() {
        $validation = new Validation(['timestamp' => ['type' => 'epoch']]);

        $data = ['timestamp' => time()];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['timestamp' => 123];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['timestamp']['code']);
        $this->assertSame("Field 'timestamp' value is not a valid epoch.", $validation->errors()['timestamp']['message']);
    }

    function test_urlValidation() {
        $validation = new Validation(['link' => ['type' => 'url']]);

        $data = ['link' => 'http://foo.com'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['link' => 'foo.com'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['link']['code']);
        $this->assertSame("Field 'link' value is not a valid URL.", $validation->errors()['link']['message']);

        // With options.
        $validation1 = new Validation(['link' => ['type' => 'url', 'spec' => '~(\w+://)?\w+\.\w+~']]);
        $validation2 = new Validation(['link' => ['type' => 'url', 'spec' => ['host']]]);

        $data = ['link' => 'foo.com'];
        $this->assertTrue($validation1->validate($data));
        $this->assertNull($validation1->errors());

        $data = ['link' => '//foo.com'];
        $this->assertTrue($validation2->validate($data));
        $this->assertNull($validation2->errors());
    }

    function test_uuidValidation() {
        $validation = new Validation(['uniq_id' => ['type' => 'uuid']]);

        $data = ['uniq_id' => 'e94ec453-f593-43ae-a50c-1fdb17842acd'];
        $this->assertTrue($validation->validate($data));
        $this->assertNull($validation->errors());

        $data = ['uniq_id' => 'invalid'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['uniq_id']['code']);
        $this->assertSame("Field 'uniq_id' value is not a valid UUID.", $validation->errors()['uniq_id']['message']);

        $data = ['uniq_id' => '00000000-0000-0000-0000-000000000000'];
        $this->assertFalse($validation->validate($data));
        $this->assertSame(ValidationError::NOT_VALID, $validation->errors()['uniq_id']['code']);
        $this->assertSame("Field 'uniq_id' value is not a valid UUID, null UUID given.", $validation->errors()['uniq_id']['message']);

        // With options.
        $validation1 = new Validation(['uniq_id' => ['type' => 'uuid', 'strict' => false]]);
        $validation2 = new Validation(['uniq_id' => ['type' => 'uuid', 'null' => true]]);

        $data = ['uniq_id' => '07f11f067e56463573bbed5763654da4'];
        $this->assertTrue($validation1->validate($data));
        $this->assertNull($validation1->errors());

        $data = ['uniq_id' => '00000000000000000000000000000000'];
        $this->assertTrue($validation2->validate($data));
        $this->assertNull($validation2->errors());
    }
}
