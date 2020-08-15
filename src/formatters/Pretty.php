<?php

namespace Gendiff\Formatters\Pretty;

use Exception;
use Funct\Collection;

use function Gendiff\Formatters\formatBooleanValue;

use const Gendiff\Formatters\END_OF_LINE;

const OPERATION_PREFIXES = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
],
INDENT_STEP = '    ';

function format(array $data): string
{
    $iter = function ($node, $linesAcc, $depth = 0) use (&$iter) {
        switch ($node['type']) {
            case 'complex':
                return [
                    getIndent($depth + 1) . "{$node['key']}: {",
                    ...array_reduce(
                        $node['children'],
                        fn($acc, $child) => [...$acc, ...$iter($child, $linesAcc, $depth + 1)],
                        []
                    ),
                    getIndent($depth + 1) . "}"
                ];
            case 'changed':
                return [
                    ...$linesAcc,
                    sprintf(
                        '  %s%s%s: %s',
                        getIndent($depth),
                        OPERATION_PREFIXES['removed'],
                        $node['key'],
                        formatValue($node['old'], $depth)
                    ),
                    sprintf(
                        '  %s%s%s: %s',
                        getIndent($depth),
                        OPERATION_PREFIXES['added'],
                        $node['key'],
                        formatValue($node['value'], $depth)
                    ),
                ];
            case 'kept':
            case 'added':
            case 'removed':
                return [
                    ...$linesAcc,
                    sprintf(
                        '  %s%s%s: %s',
                        getIndent($depth),
                        OPERATION_PREFIXES[$node['type']],
                        $node['key'],
                        formatValue($node['value'], $depth)
                    ),
                ];
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = Collection\flatten(["{", ...(array_map(fn($node) => $iter($node, []), $data)), "}"]);
    return implode(END_OF_LINE, $lines);
}

function getIndent(int $depth): string
{
    return str_repeat(INDENT_STEP, $depth);
}

function isAssocArray(array $arr): bool
{
    return array_values($arr) !== $arr;
}

function formatValue($value, int $depth): string
{
    if (is_bool($value)) {
        return formatBooleanValue($value);
    } elseif (is_array($value)) {
        $isAssoc = isAssocArray($value);
        return implode(
            END_OF_LINE,
            [
                ($isAssoc ? '{' : '['),
                ...array_map(
                    fn($key) => sprintf(
                        '  %s%s  %s%s',
                        getIndent($depth),
                        INDENT_STEP,
                        ($isAssoc ? "{$key}: " : ""),
                        formatValue($value[$key], $depth + 1)
                    ),
                    array_keys($value)
                ),
                getIndent($depth + 1) . ($isAssoc ? '}' : ']')
            ]
        );
    } else {
        return (string)$value;
    }
}
