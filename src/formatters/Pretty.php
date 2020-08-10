<?php

namespace Gendiff\Formatters\Pretty;

const OPERATIONS = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
    'changed' => '  ',
],
MARGIN = '  ';

function output($data)
{
    $iter = function ($elem, $current, $depth = 0) use (&$iter, $data) {
        $spaces = str_repeat(MARGIN, $depth * 2 + 1);
        if (isset($elem['children'])) {
            return [
                "$spaces  {$elem['key']}: {",
                ...array_reduce(
                    $elem['children'],
                    fn($acc, $i) => [...$acc, ...$iter($i, $current, $depth + 1)],
                    []
                ),
                "$spaces  }"
            ];
        }
        $operation = OPERATIONS[$elem['type']];
        if (is_array($elem['value'])) {
            return [
                ...$current,
                "$spaces{$operation}{$elem['key']}: {",
                ...array_reduce(
                    array_keys($elem['value']),
                    fn($acc, $i) => [...$acc, "$spaces      {$i}: {$elem['value'][$i]}"],
                    []
                ),
                "$spaces  }"
            ];
        }
        if (is_bool($elem['value'])) {
            $elem['value'] = $elem['value'] ? 'true' : 'false';
        }
        switch ($elem['type']) {
            case 'changed':
                return [
                    ...$current,
                    "$spaces- {$elem['key']}: {$elem['old']}",
                    "$spaces+ {$elem['key']}: {$elem['value']}"
                ];
            case 'kept':
            case 'added':
            case 'removed':
            default:
                return [...$current, "$spaces{$operation}{$elem['key']}: {$elem['value']}"];
        }
    };
    return ["{", ...array_reduce($data, fn($acc, $elem) => [...$acc, ...$iter($elem, [])], []), "}"];
}
