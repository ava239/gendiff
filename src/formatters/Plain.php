<?php

namespace Gendiff\Formatters\Plain;

function output($data)
{
    return implode("\n", outputInner($data));
}

function outputInner($data, $depth = 0, $prefix = '')
{
    $result = [];
    foreach ($data as $op => $type) {
        $result = [...$result, ...outputType($type, $prefix, $op, $depth + 1)];
    }
    return $result;
}

function outputType($data, $prefix, $type, $depth)
{
    $result = [];
    foreach ($data as $key => $change) {
        $fullkey = ($prefix ? "$prefix." : "") . $key;
        if (is_array($change) && isset($change['kept']) && !in_array($type, ['added', 'removed'])) {
            $result = [...$result, ...outputInner($change, $depth, $fullkey)];
        } else {
            $value = is_bool($change) ? ($change ? 'true' : 'false') : $change;
            switch ($type) {
                case 'added':
                    $value = is_array($value) ? 'complex value' : $value;
                    $result[] = "Property '$fullkey' was added with value: '{$value}'";
                    break;
                case 'removed':
                    $result[] = "Property '$fullkey' was removed";
                    break;
                case 'changed':
                    $result[] = "Property '$fullkey' was changed. From '{$value[0]}' to '{$value[1]}'";
                    break;
            }
        }
    }
    return $result;
}
