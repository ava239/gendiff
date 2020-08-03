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
    $result = [];
    $start = $depth > 0 ? str_repeat(MARGIN, $depth * 2 - 1) : '';
    $end = $depth > 0 ? $start . MARGIN : '';
    foreach ($data as $op => $type) {
        $result = [
            ...$result,
            ...outputType($type, $op, $depth),
        ];
    }
    $result = [
        "$start{$prefix}{",
        ...$result,
        "$end}",
    ];
    return $result;
}

function outputType($data, $type, $depth)
{
    $result = [];
    $start = str_repeat(MARGIN, $depth * 2 + 1);
    foreach ($data as $key => $change) {
        $operation = OPERATIONS[$type];
        if (is_array($change) && isset($change['kept'])) {
            $result = [
                ...$result,
                ...output($change, $depth + 1, "{$operation}{$key}: "),
            ];
        } else {
            $value = is_bool($change) ? ($change ? 'true' : 'false') : $change;
            switch ($type) {
                case 'kept':
                case 'added':
                case 'removed':
                    $result[] = "$start{$operation}$key: $value";
                    break;
                case 'changed':
                    $result[] = "$start- $key: {$value[0]}";
                    $result[] = "$start+ $key: {$value[1]}";
                    break;
            }
        }
    }
    return $result;
}
