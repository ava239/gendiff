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
    $iter = function ($node, $depth = 0) use (&$iter) {
        switch ($node['type']) {
            case 'complex':
                return [
                    getIndent($depth + 1) . "{$node['key']}: {",
                    array_map(fn($child) => $iter($child, $depth + 1), $node['children']),
                    getIndent($depth + 1) . "}"
                ];
            case 'changed':
                return [
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
                return sprintf(
                    '  %s%s%s: %s',
                    getIndent($depth),
                    OPERATION_PREFIXES[$node['type']],
                    $node['key'],
                    formatValue($node['value'], $depth)
                );
            default:
                throw new Exception("Unknown node type: '{$node['type']}'");
        }
    };
    $lines = Collection\flattenAll(["{", array_map(fn($node) => $iter($node), $data), "}"]);
    return implode(END_OF_LINE, $lines);
}

function getIndent(int $depth): string
{
    return str_repeat(INDENT_STEP, $depth);
}

function formatValue($value, int $depth): string
{
    $valueType = gettype($value);
    switch ($valueType) {
        case 'boolean':
            return formatBooleanValue($value);
        case 'object':
            return implode(
                END_OF_LINE,
                [
                    '{',
                    ...array_map(
                        fn($key) => sprintf(
                            '  %s%s  %s: %s',
                            getIndent($depth),
                            INDENT_STEP,
                            $key,
                            formatValue($value->{$key}, $depth + 1)
                        ),
                        array_keys((array)$value)
                    ),
                    getIndent($depth + 1) . '}'
                ]
            );
        case 'array':
            return implode(
                END_OF_LINE,
                [
                    '[',
                    ...array_map(
                        fn($key) => sprintf(
                            '  %s%s  %s',
                            getIndent($depth),
                            INDENT_STEP,
                            formatValue($value[$key], $depth + 1)
                        ),
                        array_keys($value)
                    ),
                    getIndent($depth + 1) . ']'
                ]
            );
        case 'NULL':
            return 'null';
        case 'integer':
        case 'double':
        case 'string':
            return (string)$value;
        default:
            throw new Exception("Unsupported value type '{$valueType}'");
    }
}
