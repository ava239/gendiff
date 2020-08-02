<?php

namespace Gendiff\Output\Pretty;

const OPERATIONS = [
    'kept' => ' ',
    'added' => '+',
    'removed' => '-',
    'changed' => ' ',
];

function output($data, $depth = 0, $param = '')
{
    $start = str_repeat('  ', $depth > 0 ? $depth * 2 - 1 : 0);
    $end = str_repeat('  ', $depth > 0 ? $depth * 2 : 0);
    $result = "$start{$param}{\n";
    foreach ($data as $op => $type) {
        $result .= outputType($type, $op, $depth + 1);
    }
    $result .= "$end}";
    if ($depth > 0) {
        $result .= "\n";
    }
    return $result;
}

function outputType($data, $type, $depth)
{
    $result = '';
    $start = str_repeat('  ', $depth * 2 - 1);
    foreach ($data as $key => $change) {
        $operation = OPERATIONS[$type];
        if (is_array($change) && isset($change['kept'])) {
            $result .= output($change, $depth, "{$operation} {$key}: ");
        } else {
            $value = is_bool($change) ? ($change ? 'true' : 'false') : $change;
            if ($type !== 'changed') {
                $result .= "$start{$operation} {$key}: {$value}\n";
            } else {
                $result .= "$start- {$key}: {$value[0]}\n";
                $result .= "$start+ {$key}: {$value[1]}\n";
            }
        }
    }
    return $result;
}
