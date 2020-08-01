<?php

namespace Gendiff\Output\Pretty;

const OPERATIONS = [
    'kept' => ' ',
    'added' => '+',
    'removed' => '-',
];

function output($data)
{
    $result = "{\n";
    foreach ($data as $op => $type) {
        foreach ($type as $key => $change) {
            $value = is_bool($change) ? ($change ? 'true' : 'false') : $change;
            if ($op !== 'changed') {
                $operation = OPERATIONS[$op];
                $result .= "  {$operation} {$key}: {$value}\n";
            } else {
                $result .= "  - {$key}: {$value[0]}\n";
                $result .= "  + {$key}: {$value[1]}\n";
            }
        }
    }
    $result .= "}";
    return $result;
}
