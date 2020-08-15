<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Core;

class DiffTest extends TestCase
{
    private const FIXTURES_PATH = __DIR__ . '/fixtures/';

    /**
     * @dataProvider formatsProvider
     */
    public function testDiff($outputFormat, $inputFormat)
    {
        $file1 = self::FIXTURES_PATH . "before." . $inputFormat;
        $file2 = self::FIXTURES_PATH . "after." . $inputFormat;
        $expectedOutput = file_get_contents(self::FIXTURES_PATH . $outputFormat . ".output");
        $this->assertEquals($expectedOutput, Core\compareFiles($file1, $file2, $outputFormat));
    }

    public function formatsProvider()
    {
        return [
            ['json', 'json'],
            ['json', 'yml'],
            ['plain', 'json'],
            ['plain', 'yml'],
            ['pretty', 'json'],
            ['pretty', 'yml'],
        ];
    }
}
