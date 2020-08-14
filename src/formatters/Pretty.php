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
    $iter = function ($node, $linesAcc, $depth = 0) use (&$iter) {
        $indent = str_repeat(INDENT_STEP, $depth) . '  ';
        $operation = OPERATION_PREFIXES[$node['type']];
        switch ($node['type']) {
            case 'complex':
                return [
                    "{$indent}  {$node['key']}: {",
                    ...array_reduce(
                        $node['children'],
                        fn($acc, $child) => [...$acc, ...$iter($child, $linesAcc, $depth + 1)],
                        []
                    ),
                    "$indent  }"
                ];
            case 'changed':
                return [
                    ...$linesAcc,
                    sprintf(
                        '%s%s%s: %s',
                        $indent,
                        OPERATION_PREFIXES['removed'],
                        $node['key'],
                        formatValue($node['old'])
                    ),
                    sprintf(
                        '%s%s%s: %s',
                        $indent,
                        OPERATION_PREFIXES['added'],
                        $node['key'],
                        formatValue($node['value'])
                    ),
                ];
            case 'kept':
            case 'added':
            case 'removed':
                if (is_array($node['value'])) {
                    return [
                        ...$linesAcc,
                        "{$indent}{$operation}{$node['key']}: {",
                        ...array_map(
                            fn($key) => sprintf(
                                '%s%s  %s: %s',
                                $indent,
                                INDENT_STEP,
                                $key,
                                formatValue($node['value'][$key])
                            ),
                            array_keys($node['value'])
                        ),
                        "$indent  }"
                    ];
                }
                return [
                    ...$linesAcc,
                    sprintf(
                        '%s%s%s: %s',
                        $indent,
                        $operation,
                        $node['key'],
                        formatValue($node['value'])
                    )
                ];
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = ["{", ...array_reduce($data, fn($acc, $node) => [...$acc, ...$iter($node, [])], []), "}"];
    return implode(PHP_EOL, $lines);
}
