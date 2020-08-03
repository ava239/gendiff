<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;
use Gendiff\Gendiff;
use Gendiff\Parsers;
use Gendiff\Core;

class YamlTest extends TestCase
{
    protected $yml1;
    protected $yml2;

    public function setUp(): void
    {
        $this->yml1 = Gendiff\readFile('tests/fixtures/before-complex.yml');
        $this->yml2 = Gendiff\readFile('tests/fixtures/after-complex.yml');
    }

    public function testParse()
    {
        $expected = [
            "common" => [
                "setting1" => "Value 1",
                "setting2" => "200",
                "setting3" => true,
                "setting6" => [
                    "key" => "value",
                ]
            ],
            "group1" => [
                "baz" => "bas",
                "foo" => "bar",
            ],
            "group2" => [
                "abc" => "12345",
            ],
        ];
        $this->assertEquals($expected, Parsers\parse($this->yml1, 'yml'));
    }

    public function testDiff()
    {
        $expected = [
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
        $data1 = Parsers\parse($this->yml1, 'yml');
        $data2 = Parsers\parse($this->yml2, 'yml');
        $this->assertEquals($expected, Core\compare($data1, $data2));
    }

    public function testWrapper()
    {
        $data1 = Parsers\parse($this->yml1, 'yml');
        $data2 = Parsers\parse($this->yml2, 'yml');
        $diff = Core\compare($data1, $data2);
        $this->assertEquals(
            Output\format($diff, 'pretty'),
            Gendiff\compareFiles('tests/fixtures/before-complex.yml', 'tests/fixtures/after-complex.yml')
        );
    }
}
