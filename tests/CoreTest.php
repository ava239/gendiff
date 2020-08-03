<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Gendiff;
use Gendiff\Parsers;

class CoreTest extends TestCase
{
    public function testMisc()
    {
        $this->assertEquals([], Parsers\parse('random text', ''));
        $this->assertEquals('json', Gendiff\detectFormat('tests/fixtures/before-complex.json'));
        $this->assertEquals('yml', Gendiff\detectFormat('tests/fixtures/before-complex.yml'));
    }
}
