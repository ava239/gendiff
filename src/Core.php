<?php

namespace Gendiff\Core;

use Exception;
use Gendiff\Parsers;
use Gendiff\Formatters;
use Funct\Collection;

function compareFiles(string $filepath1, string $filepath2, string $outputFormat = 'pretty'): string
{
    $fileContents1 = readFile($filepath1);
    $fileContents2 = readFile($filepath2);
    $format1 = detectFormat($filepath1);
    $format2 = detectFormat($filepath2);
    $object1 = Parsers\parse($fileContents1, $format1);
    $object2 = Parsers\parse($fileContents2, $format2);
    $diff = getDiff($object1, $object2);
    return Formatters\format($diff, $outputFormat);
}

function readFile(string $filePath): string
{
    $realFilepath = realpath($filePath);
    if (!is_file($realFilepath)) {
        throw new Exception("'$filePath' is not a file");
    }
    if (!is_readable($realFilepath)) {
        throw new Exception("Can`t read '$filePath'");
    }
    return file_get_contents($realFilepath);
}

function detectFormat(string $filePath): string
{
    $pathInfo = pathinfo($filePath);
    return strtolower($pathInfo['extension']);
}

function getDiff(object $object1, object $object2): array
{
    $keys1 = array_keys(get_object_vars($object1));
    $keys2 = array_keys(get_object_vars($object2));
    $nodeKeys = Collection\union($keys1, $keys2);
    $diff = array_map(function ($key) use ($object1, $object2) {
        if (!property_exists($object1, $key)) {
            return getNode(
                'added',
                $key,
                ['new' => $object2->{$key}]
            );
        }
        if (!property_exists($object2, $key)) {
            return getNode(
                'removed',
                $key,
                ['old' => $object1->{$key}]
            );
        }
        if ($object1->{$key} === $object2->{$key}) {
            return getNode(
                'kept',
                $key,
                ['old' => $object1->{$key}, 'new' => $object2->{$key}]
            );
        }
        if (is_object($object1->{$key}) && is_object($object2->{$key})) {
            return getNode(
                'complex',
                $key,
                [],
                getDiff($object1->{$key}, $object2->{$key})
            );
        }
        return getNode(
            'changed',
            $key,
            ['old' => $object1->{$key}, 'new' => $object2->{$key}]
        );
    }, $nodeKeys);
    return array_values($diff);
}

function getNode(string $type, $key, array $values, array $children = []): array
{
    return [
        'type' => $type,
        'key' => $key,
        'values' => $values,
        'children' => $children
    ];
}
