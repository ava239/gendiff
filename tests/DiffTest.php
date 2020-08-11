<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Gendiff;

class DiffTest extends TestCase
{
    protected $fixturePath = 'tests/fixtures/';
    /**
     * @dataProvider formatsProvider
     */
    public function testDiff($outputFormat, $inputFormat)
    {
        $file1 = "{$this->fixturePath}before.$inputFormat";
        $file2 = "{$this->fixturePath}after.$inputFormat";
        $expectedOutput = file_get_contents("{$this->fixturePath}{$outputFormat}.output");
        $this->assertEquals($expectedOutput, Gendiff\compareFiles($file1, $file2, $outputFormat));
    }

    public function formatsProvider()
    {
        return [
            ['pretty', 'json'],
            ['pretty', 'yml'],
            ['json', 'json'],
            ['json', 'yml'],
            /*['plain', 'json'],
            ['plain', 'yml'],*/
        ];
    }
}
