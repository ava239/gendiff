<?php

namespace Gendiff\Formatters\Pretty;

use Exception;

use function Gendiff\Formatters\formatValue;

const OPERATION_PREFIXES = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
    'changed' => '  ',
    'complex' => '  ',
],
INDENT_STEP = '    ';

function format(array $data): string
{
    $iter = function ($node, $current, $depth = 0) use (&$iter) {
        $indent = str_repeat(INDENT_STEP, $depth) . '  ';
        $operation = OPERATION_PREFIXES[$node['type']];
        switch ($node['type']) {
            case 'complex':
                return [
                    "{$indent}  {$node['key']}: {",
                    ...array_reduce(
                        $node['children'],
                        fn($acc, $child) => [...$acc, ...$iter($child, $current, $depth + 1)],
                        []
                    ),
                    "$indent  }"
                ];
            case 'changed':
                return [
                    ...$current,
                    $indent . OPERATION_PREFIXES['removed'] . $node['key'] . ": " . formatValue($node['old']),
                    $indent . OPERATION_PREFIXES['added'] . $node['key'] . ": " . formatValue($node['value']),
                ];
            case 'kept':
            case 'added':
            case 'removed':
                if (is_array($node['value'])) {
                    return [
                        ...$current,
                        "{$indent}{$operation}{$node['key']}: {",
                        ...array_map(
                            fn($key) => $indent . "      " . $key . ": " . formatValue($node['value'][$key]),
                            array_keys($node['value'])
                        ),
                        "$indent  }"
                    ];
                }
                return [...$current, $indent . $operation . $node['key'] . ": " . formatValue($node['value'])];
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = ["{", ...array_reduce($data, fn($acc, $node) => [...$acc, ...$iter($node, [])], []), "}"];
    return implode("\n", $lines);
}
