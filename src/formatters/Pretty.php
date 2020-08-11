<?php

namespace Gendiff\Formatters\Pretty;

const OPERATION_PREFIXES = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
    'changed' => '  ',
],
INDENT_STEP = '    ';

function format(array $data): string
{
    $iter = function ($elem, $current, $depth = 0) use (&$iter) {
        $indent = str_repeat(INDENT_STEP, $depth) . '  ';
        if (isset($elem['children'])) {
            return [
                "$indent  {$elem['key']}: {",
                ...array_reduce(
                    $elem['children'],
                    fn($acc, $item) => [...$acc, ...$iter($item, $current, $depth + 1)],
                    []
                ),
                "$indent  }"
            ];
        }
        $operation = OPERATION_PREFIXES[$elem['type']];
        if (is_array($elem['value'])) {
            return [
                ...$current,
                "$indent{$operation}{$elem['key']}: {",
                ...array_map(
                    fn($key) => "$indent      {$key}: {$elem['value'][$key]}",
                    array_keys($elem['value'])
                ),
                "$indent  }"
            ];
        }
        if (is_bool($elem['value'])) {
            $elem['value'] = $elem['value'] ? 'true' : 'false';
        }
        switch ($elem['type']) {
            case 'changed':
                return [
                    ...$current,
                    "$indent- {$elem['key']}: {$elem['old']}",
                    "$indent+ {$elem['key']}: {$elem['value']}"
                ];
            case 'kept':
            case 'added':
            case 'removed':
            default:
                return [...$current, "$indent{$operation}{$elem['key']}: {$elem['value']}"];
        }
    };
    $lines = ["{", ...array_reduce($data, fn($acc, $item) => [...$acc, ...$iter($item, [])], []), "}"];
    return implode("\n", $lines);
}
