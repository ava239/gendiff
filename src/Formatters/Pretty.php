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
            switch ($node['type']) {
                case 'complex':
                    return [
                        getIndent($depth + 1) . "{$node['key']}: {",
                        $format($node['children'], $depth + 1),
                        getIndent($depth + 1) . "}"
                    ];
                case 'changed':
                    return [
                        formatMessage($node['key'], OPERATIONS['removed'], $node['values']['old'], $depth),
                        formatMessage($node['key'], OPERATIONS['added'], $node['values']['new'], $depth),
                    ];
                case 'kept':
                case 'added':
                    return formatMessage($node['key'], OPERATIONS[$node['type']], $node['values']['new'], $depth);
                case 'removed':
                    return formatMessage($node['key'], OPERATIONS[$node['type']], $node['values']['old'], $depth);
                default:
                    throw new Exception("Unknown node type: '{$node['type']}'");
            }
        }, $diff);
    };
    $lines = Collection\flattenAll(["{", $format($diff), "}"]);
    return implode("\n", $lines);
}

function formatMessage(string $key, string $operation, $value, int $depth, string $formatStr = '  %s%s%s: %s'): string
{
    $formattedValue = formatValue($value, $depth + 1);
    return sprintf($formatStr, getIndent($depth), $operation, $key, $formattedValue);
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
                fn($key) => formatMessage($key, OPERATIONS['kept'], $value->$key, $depth),
                array_keys(get_object_vars($value))
            );
            return implode("\n", ['{', ...$lines, getIndent($depth) . '}']);
        case 'array':
            $lines = array_map(
                fn($value) => formatMessage('', OPERATIONS['kept'], $value, $depth, '  %s%s%s%s'),
                $value
            );
            return implode("\n", ['[', ...$lines, getIndent($depth) . ']']);
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
