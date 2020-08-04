<?php

namespace Gendiff\Formatters\Plain;

function output($data, $depth = 0, $prefix = '')
{
    return array_reduce(array_keys($data), function ($acc, $type) use ($data, $depth, $prefix) {
        $operations = $data[$type];
        return [...$acc, ...outputType($operations, $prefix, $type, $depth + 1)];
    }, []);
}

function outputType($data, $prefix, $type, $depth)
{
    return array_reduce(array_keys($data), function ($acc, $key) use ($data, $prefix, $type, $depth) {
        $value = $data[$key];
        $name = ($prefix ? "$prefix." : "") . $key;
        if (is_array($value) && isset($value['kept']) && !in_array($type, ['added', 'removed'])) {
            return [...$acc, ...output($value, $depth, $name)];
        } else {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            switch ($type) {
                case 'added':
                    $value = is_array($value) ? 'complex value' : $value;
                    return [...$acc, "Property '$name' was added with value: '{$value}'"];
                case 'removed':
                    return [...$acc, "Property '$name' was removed"];
                case 'changed':
                    return [...$acc, "Property '$name' was changed. From '{$value[0]}' to '{$value[1]}'"];
                default:
                    return $acc;
            }
        }
    }, []);
}
