<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Gendiff;

class JsonTest extends TestCase
{
    public function testResult()
    {
        $test1 = Gendiff\compare('tests/fixtures/before.json', 'tests/fixtures/after.json');
        $expected = '{
    host: hexlet.io
  - timeout: 50
  + timeout: 20
  + verbose: true
  - proxy: 123.234.53.22
}';
        $this->assertEquals($expected, $test1);
    }
}