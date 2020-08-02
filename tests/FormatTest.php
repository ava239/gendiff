<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;

class FormatTest extends TestCase
{
    public function testEmpty()
    {
        $data = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
        ];
        $this->assertEquals('', Output\format($data, ''));
    }

    public function testPretty()
    {
        $data = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
        ];
        $expected = '{
    host: hexlet.io
  - timeout: 50
  + timeout: 20
  + verbose: true
  - proxy: 123.234.53.22
}';
        $this->assertEquals($expected, Output\format($data, 'pretty'));
        $data2 = [
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
        $this->assertEquals($expected2, Output\format($data2, 'pretty'));
    }

    public function testPlain()
    {
        $data = [
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
        $expected = "Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group3' was added with value: 'complex value'
Property 'group2' was removed";
        $this->assertEquals($expected, Output\format($data, 'plain'));
    }
}
