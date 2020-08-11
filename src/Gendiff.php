<?php

namespace Gendiff\Gendiff;

use Exception;
use Gendiff\Parsers;
use Gendiff\Output;

function compareFiles(string $path1, string $path2, string $outputFormat = 'pretty'): string
{
    $fileContents1 = readFile($path1);
    $fileContents2 = readFile($path2);
    $format1 = detectFormat($path1);
    $format2 = detectFormat($path2);
    $data1 = Parsers\parse($fileContents1, $format1);
    $data2 = Parsers\parse($fileContents2, $format2);
    $diff = getDiff($data1, $data2);
    return Output\format($diff, $outputFormat);
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

function getDiff(array $data1, array $data2): array
{
    $dataKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    return array_reduce($dataKeys, function ($acc, $key) use ($data1, $data2) {
        $elem1 = $data1[$key] ?? null;
        $elem2 = $data2[$key] ?? null;
        if ($elem1 === $elem2) {
            return [...$acc, ['key' => $key, 'value' => $elem2, 'type' => 'kept']];
        }
        if (is_null($elem1)) {
            return [...$acc, ['key' => $key, 'value' => $elem2, 'type' => 'added']];
        }
        if (is_null($elem2)) {
            return [...$acc, ['key' => $key, 'value' => $elem1, 'type' => 'removed']];
        }
        if (is_array($elem1) && is_array($elem2)) {
            return [
                ...$acc,
                ['key' => $key, 'children' => getDiff($elem1, $elem2)]
            ];
        }
        return [...$acc, ['key' => $key, 'value' => $elem2, 'old' => $elem1, 'type' => 'changed']];
    }, []);
}
