<?php

namespace Gendiff\Tests;

use Error;
use PHPUnit\Framework\TestCase;
use Gendiff\Output;
use Gendiff\Parsers;
use Gendiff\Gendiff;

class ErrorsTest extends TestCase
{

    public function testFormatterException()
    {
        $this->expectException(Error::class);
        Output\format('', '');
    }

    public function testParserException()
    {
        $this->expectException(Error::class);
        Parsers\parse('', '');
    }

    public function testDirectoryPathException()
    {
        $this->expectException(Error::class);
        Gendiff\readFile('.');
    }

    public function testAbsentFilepathException()
    {
        $this->expectException(Error::class);
        Gendiff\readFile('/../file');
    }

    public function testErrorWrapper()
    {
        $this->assertStringStartsWith(
            'Error: ',
            Gendiff\compareFiles('tests/fixtures/before-complex.json', 'tests/fixtures/after-complex.jso')
        );
    }
}
