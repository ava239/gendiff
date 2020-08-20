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
        $filePath1 = self::FIXTURES_PATH . "before." . $inputFormat;
        $filePath2 = self::FIXTURES_PATH . "after." . $inputFormat;
        $expectedOutput = file_get_contents(self::FIXTURES_PATH . $outputFormat . ".output");
        $this->assertEquals($expectedOutput, Core\compareFiles($filePath1, $filePath2, $outputFormat));
    }

    public function formatsProvider()
    {
        return [
            ['plain', 'json'],
            ['plain', 'yml'],
            ['pretty', 'json'],
            ['pretty', 'yml'],
            ['json', 'json'],
            ['json', 'yml'],
        ];
    }
}
