<?php

namespace Gendiff\Formatters\Plain;

use Exception;

use function Gendiff\Formatters\formatValue;

function format(array $data): string
{
    $iter = function ($node, $current, $prefix = '') use (&$iter) {
        $fullNodeKey = $prefix . $node['key'];
        switch ($node['type']) {
            case 'complex':
                return array_reduce(
                    $node['children'],
                    fn($acc, $item) => [...$acc, ...$iter($item, [], "{$fullNodeKey}.")],
                    $current
                );
            case 'added':
                return [
                    ...$current,
                    sprintf(
                        "Property '%s' was added with value: '%s'",
                        $fullNodeKey,
                        formatValue($node['value'])
                    )
                ];
            case 'removed':
                return [
                    ...$current,
                    sprintf(
                        "Property '%s' was removed",
                        $fullNodeKey
                    )];
            case 'changed':
                return [
                    ...$current,
                    sprintf(
                        "Property '%s' was changed. From '%s' to '%s'",
                        $fullNodeKey,
                        formatValue($node['old']),
                        formatValue($node['value'])
                    )
                ];
            case 'kept':
                return $current;
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = array_reduce($data, fn($acc, $node) => [...$acc, ...$iter($node, [])], []);
    return implode("\n", $lines);
}
