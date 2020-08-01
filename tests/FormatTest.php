<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Output;

class FormatTest extends TestCase
{
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
}
