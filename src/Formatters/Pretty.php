<?php

namespace Gendiff\Formatters\Pretty;

use Exception;
use Funct\Collection;

const OPERATION_PREFIXES = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
],
INDENT_STEP = '    ';

function format(array $diff): string
{
    $format = function ($diff, $depth = 0) use (&$format) {
        return array_map(function ($node) use ($depth, $format) {
            switch ($node['type']) {
                case 'complex':
                    return [
                        getIndent($depth + 1) . "{$node['key']}: {",
                        $format($node['children'], $depth + 1),
                        getIndent($depth + 1) . "}"
                    ];
                case 'changed':
                    return [
                        sprintf(
                            '  %s%s%s: %s',
                            getIndent($depth),
                            OPERATION_PREFIXES['removed'],
                            $node['key'],
                            formatValue($node['values']['old'], $depth)
                        ),
                        sprintf(
                            '  %s%s%s: %s',
                            getIndent($depth),
                            OPERATION_PREFIXES['added'],
                            $node['key'],
                            formatValue($node['values']['new'], $depth)
                        ),
                    ];
                case 'kept':
                case 'added':
                    return sprintf(
                        '  %s%s%s: %s',
                        getIndent($depth),
                        OPERATION_PREFIXES[$node['type']],
                        $node['key'],
                        formatValue($node['values']['new'], $depth)
                    );
                case 'removed':
                    return sprintf(
                        '  %s%s%s: %s',
                        getIndent($depth),
                        OPERATION_PREFIXES[$node['type']],
                        $node['key'],
                        formatValue($node['values']['old'], $depth)
                    );
                default:
                    throw new Exception("Unknown node type: '{$node['type']}'");
            }
        }, $diff);
    };
    $lines = Collection\flattenAll(["{", $format($diff), "}"]);
    return implode("\n", $lines);
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
            return $value ? 'true' : 'false';
        case 'object':
            $lines = array_map(
                fn($key) => sprintf(
                    '  %s%s  %s: %s',
                    getIndent($depth),
                    INDENT_STEP,
                    $key,
                    formatValue($value->{$key}, $depth + 1)
                ),
                array_keys(get_object_vars($value))
            );
            return implode(
                "\n",
                ['{', ...$lines, getIndent($depth + 1) . '}']
            );
        case 'array':
            $lines = array_map(
                fn($key) => sprintf(
                    '  %s%s  %s',
                    getIndent($depth),
                    INDENT_STEP,
                    formatValue($value[$key], $depth + 1)
                ),
                array_keys($value)
            );
            return implode(
                "\n",
                ['[', ...$lines, getIndent($depth + 1) . ']']
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
