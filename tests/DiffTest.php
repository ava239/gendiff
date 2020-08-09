<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Gendiff;
use Gendiff\Output;

class DiffTest extends TestCase
{
    protected $diff;

    public function setUp(): void
    {
        $this->diff = [
            'kept' => [
                'common' => [
                    'kept' => [
                        'setting1' => 'Value 1',
                        'setting3' => true,
                        'setting9' => [
                            'kept' => [],
                            'changed' => [],
                            'added' => ['keys' => '1'],
                            'removed' => [],
                        ],
                    ],
                    'changed' => [],
                    'added' => [
                        'setting4' => 'blah blah',
                        'setting5' => [
                            'kept' => ['key5' => 'value5'],
                            'changed' => [],
                            'added' => [],
                            'removed' => [],
                        ],
                    ],
                    'removed' => [
                        'setting2' => '200',
                        'setting6' => [
                            'kept' => ['key' => 'value'],
                            'changed' => [],
                            'added' => [],
                            'removed' => [],
                        ],
                    ],
                ],
                'group1' => [
                    'kept' => ['foo' => 'bar'],
                    'changed' => ['baz' => ['bas', 'bars']],
                    'added' => [],
                    'removed' => [],
                ]
            ],
            'changed' => [],
            'added' => ['group3' => [
                'kept' => ['fee' => '100500'],
                'changed' => [],
                'added' => [],
                'removed' => [],
            ]],
            'removed' => ['group2' => [
                'kept' => ['abc' => '12345'],
                'changed' => [],
                'added' => [],
                'removed' => [],
            ]],
        ];
    }

    /**
     * @dataProvider filepathProvider
     */
    public function testDiff($filepath1, $filepath2)
    {
        $this->assertEquals($this->diff, Gendiff\diffFiles($filepath1, $filepath2));
    }

    public function filepathProvider()
    {
        return [
            'json' => ['tests/fixtures/before-complex.json', 'tests/fixtures/after-complex.json'],
            'yaml' => ['tests/fixtures/before-complex.yml', 'tests/fixtures/after-complex.yml'],
        ];
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testFormatters($expectedOutput, $format)
    {
        $this->assertEquals($expectedOutput, Output\format($this->diff, $format));
    }

    public function formatterProvider()
    {
        $pretty = '{
    common: {
        setting1: Value 1
        setting3: true
        setting9: {
          + keys: 1
        }
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
      - setting2: 200
      - setting6: {
            key: value
        }
    }
    group1: {
        foo: bar
      - baz: bas
      + baz: bars
    }
  + group3: {
        fee: 100500
    }
  - group2: {
        abc: 12345
    }
}';
        $plain = "Property 'common.setting9.keys' was added with value: '1'
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group3' was added with value: 'complex value'
Property 'group2' was removed";
        $json = json_encode([
            'common' => [
                'value' => [
                    'setting1' => ['value' => 'Value 1', 'type' => 'kept'],
                    'setting3' => ['value' => true, 'type' => 'kept'],
                    'setting9' => ['value' => [
                        'keys' => ['value' => '1', 'type' => 'added'],
                    ], 'type' => 'kept'],
                    'setting4' => [
                        'value' => 'blah blah',
                        'type' => 'added',
                    ],
                    'setting5' => [
                        'value' => [
                            'key5' => ['value' => 'value5', 'type' => 'kept']
                        ],
                        'type' => 'added',
                    ],
                    'setting2' => [
                        'value' => '200',
                        'type' => 'removed',
                    ],
                    'setting6' => [
                        'value' => [
                            'key' => ['value' => 'value', 'type' => 'kept']
                        ],
                        'type' => 'removed',
                    ],
                ],
                'type' => 'kept',
            ],
            'group1' => [
                'value' => [
                    'foo' => ['value' => 'bar', 'type' => 'kept'],
                    'baz' => ['value' => 'bars', 'old' => 'bas', 'type' => 'changed'],
                ],
                'type' => 'kept',
            ],
            'group3' => [
                'value' => [
                    'fee' => ['value' => '100500', 'type' => 'kept'],
                ],
                'type' => 'added',
            ],
            'group2' => [
                'value' => [
                    'abc' => ['value' => '12345', 'type' => 'kept'],
                ],
                'type' => 'removed',
            ]
        ]);
        return [
            [$pretty, 'pretty'],
            [$plain, 'plain'],
            [$json, 'json'],
        ];
    }
}
