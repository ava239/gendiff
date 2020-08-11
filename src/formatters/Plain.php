<?php

namespace Gendiff\Formatters\Plain;

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
        if (is_bool($elem['value'])) {
            $elem['value'] = $elem['value'] ? 'true' : 'false';
        }
        switch ($elem['type']) {
            case 'added':
                $value = is_array($elem['value']) ? 'complex value' : $elem['value'];
                return [...$current, "Property '$prefix{$elem['key']}' was added with value: '{$value}'"];
            case 'removed':
                return [...$current, "Property '$prefix{$elem['key']}' was removed"];
            case 'changed':
                return [
                    ...$current,
                    "Property '$prefix{$elem['key']}' was changed. From '{$elem['old']}' to '{$elem['value']}'"
                ];
            default:
                return $current;
        }
    };
    $lines = array_reduce($data, fn($acc, $elem) => [...$acc, ...$iter($elem, [])], []);
    return implode("\n", $lines);
}
