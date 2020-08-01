<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;
use Gendiff\Gendiff;
use Gendiff\FormatParser;
use Gendiff\Core;

class JsonTest extends TestCase
{
    protected $file1;
    protected $file2;

    public function setUp(): void
    {
        $this->file1 = Gendiff\readFile('tests/fixtures/before.json');
        $this->file2 = Gendiff\readFile('tests/fixtures/after.json');
    }

    public function testMisc()
    {
        $this->assertEquals('json', Gendiff\detectFormat('tests/fixtures/before.json'));
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
        $this->assertEquals('', Output\format($data, ''));
        $this->assertEquals($expected, Output\format($data, 'pretty'));
    }

    public function testParse()
    {
        $expected = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
        ];
        $this->assertEquals([], FormatParser\parse($this->file1, ''));
        $this->assertEquals($expected, FormatParser\parse($this->file1, 'json'));
    }

    public function testDiff()
    {
        $expected = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
        ];
        $data1 = FormatParser\parse($this->file1, 'json');
        $data2 = FormatParser\parse($this->file2, 'json');
        $this->assertEquals($expected, Core\compare($data1, $data2));
    }

    public function testWrapper()
    {
        $data1 = FormatParser\parse($this->file1, 'json');
        $data2 = FormatParser\parse($this->file2, 'json');
        $diff = Core\compare($data1, $data2);
        $this->assertEquals(
            Output\format($diff, 'pretty'),
            Gendiff\compareFiles('tests/fixtures/before.json', 'tests/fixtures/after.json')
        );
    }
}
