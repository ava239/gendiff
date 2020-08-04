<?php

namespace Gendiff\Formatters\Pretty;

const OPERATIONS = [
    'kept' => '  ',
    'added' => '+ ',
    'removed' => '- ',
    'changed' => '  ',
],
MARGIN = '  ';

function output($data, $depth = 0, $prefix = '')
{
    $start = '';
    if ($depth > 0) {
        $start = str_repeat(MARGIN, $depth * 2 - 1);
    }
    $end = str_repeat(MARGIN, $depth * 2);
    $lines = array_reduce(array_keys($data), function ($acc, $type) use ($data, $depth) {
        $operations = $data[$type];
        return [...$acc, ...outputType($operations, $type, $depth)];
    }, []);
    return ["$start{$prefix}{", ...$lines, "$end}"];
}

function outputType($data, $type, $depth)
{
    $spaces = str_repeat(MARGIN, $depth * 2 + 1);
    return array_reduce(array_keys($data), function ($acc, $key) use ($data, $type, $depth, $spaces) {
        $operation = OPERATIONS[$type];
        $value = $data[$key];
        if (is_array($value) && isset($value['kept'])) {
            return [...$acc, ...output($value, $depth + 1, "{$operation}{$key}: ")];
        } else {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            switch ($type) {
                case 'changed':
                    return [...$acc, "$spaces- $key: {$value[0]}", "$spaces+ $key: {$value[1]}"];
                case 'kept':
                case 'added':
                case 'removed':
                default:
                    return [...$acc, "$spaces{$operation}$key: $value"];
            }
        }
    }, []);
}
