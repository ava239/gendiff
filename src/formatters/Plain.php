<?php

namespace Gendiff\Formatters\Plain;

use function Gendiff\Output\formatValue;

function format(array $data): string
{
    $iter = function ($elem, $current, $prefix = '') use (&$iter) {
        if (isset($elem['children'])) {
            return array_reduce(
                $elem['children'],
                fn($acc, $item) => [...$acc, ...$iter($item, [], "$prefix{$elem['key']}.")],
                $current
            );
        }
        switch ($elem['type']) {
            case 'added':
                $value = is_array($elem['value']) ? 'complex value' : $elem['value'];
                return [
                    ...$current,
                    "Property '$prefix{$elem['key']}' was added with value: '" . formatValue($value) . "'"
                ];
            case 'removed':
                return [...$current, "Property '$prefix{$elem['key']}' was removed"];
            case 'changed':
                [$old, $new] = array_map('Gendiff\Output\formatValue', [$elem['old'], $elem['value']]);
                return [
                    ...$current,
                    "Property '$prefix{$elem['key']}' was changed. From '$old' to '$new'"
                ];
            default:
                return $current;
        }
    };
    $lines = array_reduce($data, fn($acc, $elem) => [...$acc, ...$iter($elem, [])], []);
    return implode("\n", $lines);
}
