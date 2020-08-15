<?php

namespace Gendiff\Formatters\Plain;

use Exception;

use function Gendiff\Formatters\formatValue;
use const Gendiff\Formatters\END_OF_LINE;

function format(array $data): string
{
    $iter = function ($node, $linesAcc, $prefix = '') use (&$iter) {
        $fullNodeKey = $prefix . $node['key'];
        switch ($node['type']) {
            case 'complex':
                return array_reduce(
                    $node['children'],
                    fn($acc, $item) => [...$acc, ...$iter($item, [], "{$fullNodeKey}.")],
                    $linesAcc
                );
            case 'added':
                return [
                    ...$linesAcc,
                    sprintf(
                        "Property '%s' was added with value: '%s'",
                        $fullNodeKey,
                        formatValue($node['value'])
                    )
                ];
            case 'removed':
                return [
                    ...$linesAcc,
                    sprintf(
                        "Property '%s' was removed",
                        $fullNodeKey
                    )];
            case 'changed':
                return [
                    ...$linesAcc,
                    sprintf(
                        "Property '%s' was changed. From '%s' to '%s'",
                        $fullNodeKey,
                        formatValue($node['old']),
                        formatValue($node['value'])
                    )
                ];
            case 'kept':
                return $linesAcc;
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = array_reduce($data, fn($acc, $node) => [...$acc, ...$iter($node, [])], []);
    return implode(END_OF_LINE, $lines);
}
