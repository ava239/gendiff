<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;

class FormatTest extends TestCase
{
    protected $data;
    protected $data2;

    public function setUp(): void
    {
        $this->data = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
        ];
        $this->data2 = [
            'kept' => [
                'common' => [
                    'kept' => [
                        'setting1' => 'Value 1',
                        'setting3' => true
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
                        ]
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

    public function testEmpty()
    {
        $this->assertEquals('', Output\format($this->data, ''));
    }

    public function testPretty()
    {
        $expected = '{
    host: hexlet.io
  - timeout: 50
  + timeout: 20
  + verbose: true
  - proxy: 123.234.53.22
}';
        $this->assertEquals($expected, Output\format($this->data, 'pretty'));
        $expected2 = '{
    common: {
        setting1: Value 1
        setting3: true
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
        $this->assertEquals($expected2, Output\format($this->data2, 'pretty'));
    }

    public function testPlain()
    {
        $expected = "Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group3' was added with value: 'complex value'
Property 'group2' was removed";
        $this->assertEquals($expected, Output\format($this->data2, 'plain'));
    }

    public function testJson()
    {
        $e2 = [
            'common' => [
                'value' => [
                    'setting1' => ['value' => 'Value 1', 'type' => 'kept'],
                    'setting3' => ['value' => true, 'type' => 'kept'],
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
        ];
        $this->assertEquals(json_encode($e2), Output\format($this->data2, 'json'));
    }
}
