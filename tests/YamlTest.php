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
        $this->yml1 = Gendiff\readFile('tests/fixtures/before.yml');
        $this->yml2 = Gendiff\readFile('tests/fixtures/after.yml');
    }

    public function testParse()
    {
        $expected = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
        ];
        $this->assertEquals($expected, Parsers\parse($this->yml1, 'yml'));
    }

    public function testDiff()
    {
        $expected = [
            'kept' => ['host' => 'hexlet.io'],
            'changed' => ['timeout' => [50, 20]],
            'added' => ['verbose' => true],
            'removed' => ['proxy' => '123.234.53.22'],
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
            Gendiff\compareFiles('tests/fixtures/before.yml', 'tests/fixtures/after.yml')
        );
    }
}
