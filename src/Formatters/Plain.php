<?php

namespace Gendiff\Formatters\Plain;

use Exception;
use Funct\Collection;

function format(array $diff): string
{
    $format = function ($diff, $prefix = '') use (&$format) {
        return array_map(function ($node) use ($prefix, $format) {
            $fullNodeKey = $prefix . $node['key'];
            switch ($node['type']) {
                case 'added':
                    return sprintf(
                        "Property '%s' was added with value: %s",
                        $fullNodeKey,
                        formatValue($node['values']['new'])
                    );
                case 'removed':
                    return sprintf("Property '%s' was removed", $fullNodeKey);
                case 'changed':
                    return sprintf(
                        "Property '%s' was changed. From %s to %s",
                        $fullNodeKey,
                        formatValue($node['values']['old']),
                        formatValue($node['values']['new'])
                    );
                case 'kept':
                    return [];
                case 'complex':
                    return $format($node['children'], "{$fullNodeKey}.");
                default:
                    throw new Exception("Unknown node type: '{$node['type']}'");
            }
        }, $diff);
    };
    $lines = Collection\flattenAll($format($diff));
    return implode("\n", $lines);
}

function formatValue($value): string
{
    $valueType = gettype($value);
    $addQuotes = fn($value) => sprintf("'%s'", $value);
    switch ($valueType) {
        case 'boolean':
            $formattedValue = $value ? 'true' : 'false';
            return $addQuotes($formattedValue);
        case 'object':
        case 'array':
            return $addQuotes('complex value');
        case 'NULL':
            return 'null';
        case 'integer':
        case 'double':
        case 'string':
            return $addQuotes((string)$value);
        default:
            throw new Exception("Unsupported value type '{$valueType}'");
    }
}
