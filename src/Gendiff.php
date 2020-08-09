<?php

namespace Gendiff\Gendiff;

use Error;
use Gendiff\Parsers;
use Gendiff\Core;
use Gendiff\Output;

function compareFiles($first, $second, $format = 'pretty')
{
    try {
        return Output\format(diffFiles($first, $second), $format);
    } catch (Error $e) {
        return "Error: {$e->getMessage()}";
    }
}

function diffFiles($first, $second)
{
    $file1 = readFile($first);
    $file2 = readFile($second);
    $format1 = detectFormat($first);
    $format2 = detectFormat($second);
    $object1 = Parsers\parse($file1, $format1);
    $object2 = Parsers\parse($file2, $format2);
    return Core\compare($object1, $object2);
}

function readFile($file)
{
    $path = realpath($file);
    if ($path === false) {
        throw new Error("Filepath '$file' does not exist");
    }
    if (is_dir($path)) {
        throw new Error("'$file' is a directory");
    }
    return file_get_contents($path);
}

function detectFormat($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}
