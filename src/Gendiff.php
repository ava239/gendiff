<?php

namespace Gendiff\Gendiff;

use Error;
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
        throw new Error("'$file' is not a file");
    }
    if (!is_readable($path)) {
        throw new Error("Can`t read '$file'");
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
    $dataKeys = array_merge(array_keys($data1), array_keys($data2));
    $removed = array_filter($dataKeys, fn($key) => !in_array($key, array_keys($data2)));
    $added = array_filter($dataKeys, fn($key) => !in_array($key, array_keys($data1)));
    $changed = array_filter($dataKeys, fn($key) => !in_array($key, $added) && !in_array($key, $removed));
    $innerCompare = function ($data1, $data2) {
        if (is_array($data1) && is_array($data2)) {
            return getDiff($data1, $data2);
        } else {
            return $data1;
        }
    };
    $diff = array_reduce($changed, function ($acc, $index) use ($data1, $data2, $innerCompare) {
        if (is_array($data1[$index]) || $data1[$index] === $data2[$index]) {
            $acc['kept'][$index] = $innerCompare($data1[$index], $data2[$index]);
        } else {
            $acc['changed'][$index] = [
                $innerCompare($data1[$index], $data2[$index]),
                $innerCompare($data2[$index], $data1[$index])
            ];
        }
        return $acc;
    }, ['kept' => [], 'changed' => [], 'removed' => [], 'added' => []]);
    $diff = array_reduce($added, function ($acc, $index) use ($data1, $data2, $innerCompare) {
        $acc['added'][$index] = $innerCompare($data2[$index], $data2[$index]);
        return $acc;
    }, $diff);
    $diff = array_reduce($removed, function ($acc, $index) use ($data1, $data2, $innerCompare) {
        $acc['removed'][$index] = $innerCompare($data1[$index], $data1[$index]);
        return $acc;
    }, $diff);
    return $diff;
}
