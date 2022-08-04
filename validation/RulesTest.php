<?php declare(strict_types=1);
namespace test\froq\validation;
use froq\validation\{Rules, Validation};

class RulesTest extends \TestCase
{
    function test_constructor() {
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
        $this->assertSame(Validation::TYPE_STRING, $rules->user->fieldOptions['image']['@fields']['id']['type']);
    }
}
