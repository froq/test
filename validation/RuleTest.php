<?php declare(strict_types=1);
namespace test\froq\validation;
use froq\validation\{Rule, ValidationType, ValidationError, ValidationException};

class RuleTest extends \TestCase
{
    function test_constructor() {
        $rule = new Rule('id', ['type' => 'int']);
        $this->assertSame('id', $rule->field);
        $this->assertSame('int', $rule->fieldOptions['type']);
        $this->assertSame(ValidationType::INT, $rule->fieldOptions['type']);

        // Invalids.
        try {
            $rule = new Rule('', ['type' => 'int']);
        } catch (ValidationException $e) {
            $this->assertSame('Field cannot be empty', $e->getMessage());
        }

        try {
            $rule = new Rule('id', []);
        } catch (ValidationException $e) {
            $this->assertSame('Field options cannot be empty', $e->getMessage());
        }

        try {
            $rule = new Rule('id', ['type' => 'foo']);
        } catch (ValidationException $e) {
            $this->assertStringStartsWith('Option "type" is invalid (given type: foo, available types: ',
                $e->getMessage());
        }

        try {
            $rule = new Rule('status', ['type' => 'enum']);
        } catch (ValidationException $e) {
            $this->assertStringStartsWith('Option "type.enum" requires "spec" definition as array in options ',
                $e->getMessage());
        }

        try {
            $rule = new Rule('status', ['type' => 'enum', 'spec' => 123]);
        } catch (ValidationException $e) {
            $this->assertStringStartsWith('Invalid "spec" given, only an array accepted for enum types ',
                $e->getMessage());
        }

        try {
            $rule = new Rule('status', ['type' => 'json', 'spec' => 'foo']);
        } catch (ValidationException $e) {
            $this->assertStringStartsWith('Invalid "spec" given, only array and object accepted for json types ',
                $e->getMessage());
        }

        // Spec / Spec Type.
        $rule = new Rule('bday', ['type' => 'date']);
        $this->assertSame('Y-m-d', $rule->fieldOptions['spec']);
        $this->assertSame('string', $rule->fieldOptions['specType']);

        $rule = new Rule('bday', ['type' => 'date', 'spec' => 'd/m/Y']);
        $this->assertSame('d/m/Y', $rule->fieldOptions['spec']);

        $rule = new Rule('endedAt', ['type' => 'time']);
        $this->assertSame('H:i:s', $rule->fieldOptions['spec']);

        $rule = new Rule('endedAt', ['type' => 'time', 'spec' => 'H:i']);
        $this->assertSame('H:i', $rule->fieldOptions['spec']);

        $rule = new Rule('createdAt', ['type' => 'datetime']);
        $this->assertSame('Y-m-d H:i:s', $rule->fieldOptions['spec']);

        $rule = new Rule('createdAt', ['type' => 'datetime', 'spec' => 'd/m/Y H:i:s']);
        $this->assertSame('d/m/Y H:i:s', $rule->fieldOptions['spec']);

        $rule = new Rule('name', ['type' => 'string', 'spec' => static fn($s) => strlen($s) > 2]);
        $this->assertSame('callback', $rule->fieldOptions['specType']);
        $this->assertInstanceOf(\Closure::class, $rule->fieldOptions['spec']);

        $rule = new Rule('name', ['type' => 'string', 'spec' => '~^(\w{2,})$~u']);
        $this->assertSame('regexp', $rule->fieldOptions['specType']);

        $rule = new Rule('name', ['type' => 'string', 'spec' => new \RegExp('^(\w{2,})$', 'u')]);
        $this->assertSame('regexp', $rule->fieldOptions['specType']);

        // Boolable Options.
        $rule = new Rule('id', ['type' => 'int', 'required', 'unsigned']);
        $this->assertTrue($rule->fieldOptions['required']);
        $this->assertTrue($rule->fieldOptions['unsigned']);

        $rule = new Rule('name', ['type' => 'uuid', 'strict']);
        $this->assertTrue($rule->fieldOptions['strict']);

        $rule = new Rule('rate', ['type' => 'number', 'nullable', 'strict', 'drop']);
        $this->assertTrue($rule->fieldOptions['nullable']);
        $this->assertTrue($rule->fieldOptions['strict']);
        $this->assertTrue($rule->fieldOptions['drop']);
    }

    function test_okay() {
        $rule = new Rule('id', ['type' => 'int', 'required', 'unsigned']);

        $input = '123';
        $this->assertTrue($rule->okay($input));
        $this->assertNull($rule->error());
        $this->assertIsInt($input); // Type cast (byref).

        $input = 'foo';
        $this->assertFalse($rule->okay($input));
        $this->assertNotNull($error = $rule->error());
        $this->assertIsNotInt($input);

        $this->assertSame(ValidationError::TYPE, $error['code']);
        $this->assertSame("Field 'id' value must be int, string given.", $error['message']);

        $input = '';
        $this->assertFalse($rule->okay($input));
        $this->assertNotNull($error = $rule->error());
        $this->assertIsNotInt($input);

        $this->assertSame(ValidationError::REQUIRED, $error['code']);
        $this->assertSame("Field 'id' is required, none given.", $error['message']);
    }
}
