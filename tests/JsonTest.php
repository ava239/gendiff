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
        $this->json1 = Gendiff\readFile('tests/fixtures/before.json');
        $this->json2 = Gendiff\readFile('tests/fixtures/after.json');
    }

    public function testParse()
    {
        $expected = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
        ];
        $this->assertEquals($expected, Parsers\parse($this->json1, 'json'));
    }

    public function testDiff()
    {
        $expected = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
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
            Gendiff\compareFiles('tests/fixtures/before.json', 'tests/fixtures/after.json')
        );
    }
}
