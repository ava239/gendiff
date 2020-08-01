<?php

namespace Gendiff\Gendiff;

use Gendiff\FormatParser;
use Gendiff\Core;
use Gendiff\Output;

function compareFiles($first, $second, $format = 'pretty')
{
    $file1 = readFile($first);
    $file2 = readFile($second);
    $format1 = detectFormat($first);
    $format2 = detectFormat($second);
    $object1 = FormatParser\parse($file1, $format1);
    $object2 = FormatParser\parse($file2, $format2);
    $diff = Core\compare($object1, $object2);
    return Output\format($diff, $format);
}

function readFile($file)
{
    $path = realpath($file);
    return file_get_contents($path);
}

function detectFormat($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}
