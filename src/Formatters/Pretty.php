<?php

namespace Gendiff\Formatters\Pretty;

use Exception;
use Funct\Collection;

const OPERATIONS = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
],
INDENT_STEP = '    ';

function format(array $diff): string
{
    $format = function ($diff, $depth = 0) use (&$format) {
        return array_map(function ($node) use ($depth, $format) {
            $indent = getIndent($depth);
            switch ($node['type']) {
                case 'complex':
                    return [
                        getIndent($depth + 1) . "{$node['key']}: {",
                        $format($node['children'], $depth + 1),
                        getIndent($depth + 1) . "}"
                    ];
                case 'changed':
                    $formattedOld = formatValue($node['values']['old'], $depth);
                    $formattedNew = formatValue($node['values']['new'], $depth);
                    return [
                        sprintf('  %s%s%s: %s', $indent, OPERATIONS['removed'], $node['key'], $formattedOld),
                        sprintf('  %s%s%s: %s', $indent, OPERATIONS['added'], $node['key'], $formattedNew),
                    ];
                case 'kept':
                case 'added':
                    $formattedValue = formatValue($node['values']['new'], $depth);
                    return sprintf('  %s%s%s: %s', $indent, OPERATIONS[$node['type']], $node['key'], $formattedValue);
                case 'removed':
                    $formattedValue = formatValue($node['values']['old'], $depth);
                    return sprintf('  %s%s%s: %s', $indent, OPERATIONS[$node['type']], $node['key'], $formattedValue);
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
                fn($key) => sprintf('  %s  %s: %s', getIndent($depth + 1), $key, formatValue($value->$key, $depth + 1)),
                array_keys(get_object_vars($value))
            );
            return implode(
                "\n",
                ['{', ...$lines, getIndent($depth + 1) . '}']
            );
        case 'array':
            $lines = array_map(
                fn($key) => sprintf('  %s  %s', getIndent($depth + 1), formatValue($value[$key], $depth + 1)),
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
