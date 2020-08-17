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
    $nodeNames = array_keys(array_merge((array)($data1), (array)($data2)));
    return array_map(function ($key) use ($data1, $data2) {
        if (!property_exists($data1, $key)) {
            return getNode('added', $key, $data2->{$key});
        }
        if (!property_exists($data2, $key)) {
            return getNode('removed', $key, $data1->{$key});
        }
        if ($data1->{$key} === $data2->{$key}) {
            return getNode('kept', $key, $data1->{$key});
        }
        if (is_object($data1->{$key}) && is_object($data2->{$key})) {
            return getNode('complex', $key, getDiff($data1->{$key}, $data2->{$key}));
        }
        return getNode('changed', $key, $data2->{$key}, $data1->{$key});
    }, $nodeNames);
}

function getNode(string $type, $key, ...$nodeData): array
{
    $node = ['type' => $type, 'key' => $key];
    switch ($type) {
        case 'added':
        case 'removed':
        case 'kept':
            [$node['value']] = $nodeData;
            break;
        case 'changed':
            [$node['value'], $node['old']] = $nodeData;
            break;
        case 'complex':
            [$node['children']] = $nodeData;
            break;
        default:
            throw new Exception("Unknown node type: {$type}");
    }
    return $node;
}
