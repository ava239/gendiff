<?php

namespace Gendiff\Gendiff;

use Funct\Strings;
use Gendiff\Formats\Json;
use Gendiff\Output\Pretty;

use function cli\line;

function outputDiff($file1, $file2)
{
    $diff = compare($file1, $file2);
    line($diff);
}

function compare($first, $second, $format = 'json')
{
    $file1 = readFile($first);
    $file2 = readFile($second);
    $object1 = parseFormat($file1, $format);
    $object2 = parseFormat($file2, $format);
    $diff = Json\compare($object1, $object2);
    return $diff;
}

function readFile($file)
{
    $isCurrentDir = ! Strings\startsWith($file, '/') &&
        ! Strings\startsWith($file, '~') &&
        ! Strings\startsWith($file, '.');
    if ($isCurrentDir) {
        $filePath = "./$file";
    } else {
        $filePath = $file;
    }
    return file_get_contents($filePath);
}

function parseFormat($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);
    }
    return null;
}

function printResult($data, $format)
{
    switch ($format) {
        case 'pretty':
            return Pretty\output($data);
    }
    return null;
}
