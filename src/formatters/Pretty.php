<?php

namespace Gendiff\Formatters\Pretty;

use function Gendiff\Output\formatValue;

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
                    fn($key) => "$indent      {$key}: " . formatValue($elem['value'][$key]),
                    array_keys($elem['value'])
                ),
                "$indent  }"
            ];
        }
        switch ($elem['type']) {
            case 'changed':
                [$old, $new] = array_map('Gendiff\Output\formatValue', [$elem['old'], $elem['value']]);
                return [
                    ...$current,
                    "$indent- {$elem['key']}: $old",
                    "$indent+ {$elem['key']}: $new"
                ];
            case 'kept':
            case 'added':
            case 'removed':
            default:
                return [...$current, "$indent{$operation}{$elem['key']}: " . formatValue($elem['value'])];
        }
    };
    $lines = ["{", ...array_reduce($data, fn($acc, $item) => [...$acc, ...$iter($item, [])], []), "}"];
    return implode("\n", $lines);
}
