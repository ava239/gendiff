<?php

namespace Gendiff\Formatters\Json;

function output($data, $depth = 0)
{
    return array_reduce(array_keys($data), function ($acc, $i) use ($data, $depth) {
        $type = $data[$i];
        return array_merge($acc, outputType($type, $i, $depth));
    }, []);
}

function outputType($data, $type, $depth)
{
    return array_reduce(array_keys($data), function ($acc, $i) use ($data, $depth, $type) {
        $change = $data[$i];
        if (is_array($change) && isset($change['kept'])) {
            return array_merge($acc, [
                $i => [
                    'value' => output($change, $depth + 1),
                    'type' => $type,
                ]
            ]);
        } else {
            if ($type === 'changed') {
                return array_merge($acc, [$i => ['value' => $change[1], 'old' => $change[0], 'type' => $type]]);
            } else {
                return array_merge($acc, [$i => ['value' => $change, 'type' => $type]]);
            }
        }
    }, []);
}
