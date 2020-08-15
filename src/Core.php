<?php

namespace Gendiff\Core;

use Exception;
use Gendiff\Parsers;
use Gendiff\Formatters;

function compareFiles(string $path1, string $path2, string $outputFormat = 'pretty'): string
{
    $fileContents1 = readFile($path1);
    $fileContents2 = readFile($path2);
    $format1 = detectFormat($path1);
    $format2 = detectFormat($path2);
    $data1 = Parsers\parse($fileContents1, $format1);
    $data2 = Parsers\parse($fileContents2, $format2);
    $diff = getDiff($data1, $data2);
    return Formatters\format($diff, $outputFormat);
}

function readFile(string $file): string
{
    $path = realpath($file);
    if (!is_file($path)) {
        throw new Exception("'$file' is not a file");
    }
    if (!is_readable($path)) {
        throw new Exception("Can`t read '$file'");
    }
    return file_get_contents($path);
}

function detectFormat(string $file): string
{
    $info = pathinfo($file);
    return strtolower($info['extension']);
}

function getDiff(object $data1, object $data2): array
{
    $nodeNames = array_unique(array_keys(array_merge((array)($data1), (array)($data2))));
    return array_reduce($nodeNames, function ($acc, $key) use ($data1, $data2) {
        $node1 = $data1->{$key} ?? null;
        $node2 = $data2->{$key} ?? null;
        if ($node1 === $node2) {
            return [...$acc, ['key' => $key, 'value' => $node2, 'type' => 'kept']];
        }
        if (is_null($node1)) {
            return [...$acc, ['key' => $key, 'value' => $node2, 'type' => 'added']];
        }
        if (is_null($node2)) {
            return [...$acc, ['key' => $key, 'value' => $node1, 'type' => 'removed']];
        }
        if (is_object($node1) && is_object($node2)) {
            return [
                ...$acc,
                ['key' => $key, 'children' => getDiff($node1, $node2), 'value' => 'complex value', 'type' => 'complex']
            ];
        }
        return [...$acc, ['key' => $key, 'value' => $node2, 'old' => $node1, 'type' => 'changed']];
    }, []);
}
