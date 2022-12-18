<?php declare(strict_types=1);
namespace test\froq\validation;
use froq\validation\{Rules, ValidationType};

class RulesTest extends \TestCase
{
    function testConstructor() {
        $rules = new Rules([
            'user' => [
                'image' => [
                    '@fields' => [
                        'id' => ['type' => 'string'],
                        'url' => ['type' => 'url'],
                        /* .. */
                    ]
                ]
            ]
        ]);
        $this->assertSame('user', $rules->user->field);
        $this->assertSame('string', $rules->user->fieldOptions['image']['@fields']['id']['type']);
        $this->assertSame(ValidationType::STRING, $rules->user->fieldOptions['image']['@fields']['id']['type']);
    }
}
