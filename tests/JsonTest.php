<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;
use Gendiff\Gendiff;
use Gendiff\Parsers;
use Gendiff\Core;

class JsonTest extends TestCase
{
    protected $json1;
    protected $json2;

    public function setUp(): void
    {
        $this->json1 = Gendiff\readFile('tests/fixtures/before-complex.json');
        $this->json2 = Gendiff\readFile('tests/fixtures/after-complex.json');
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
                ],
                "setting9" => [],
            ],
            "group1" => [
                "baz" => "bas",
                "foo" => "bar",
            ],
            "group2" => [
                "abc" => "12345",
            ],
        ];
        $this->assertEquals($expected, Parsers\parse($this->json1, 'json'));
    }

    public function testDiff()
    {
        $expected = [
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
        $data1 = Parsers\parse($this->json1, 'json');
        $data2 = Parsers\parse($this->json2, 'json');
        $this->assertEquals($expected, Core\compare($data1, $data2));
    }

    public function testWrapper()
    {
        $data1 = Parsers\parse($this->json1, 'json');
        $data2 = Parsers\parse($this->json2, 'json');
        $diff = Core\compare($data1, $data2);
        $this->assertEquals(
            Output\format($diff, 'pretty'),
            Gendiff\compareFiles('tests/fixtures/before-complex.json', 'tests/fixtures/after-complex.json')
        );
    }
}
