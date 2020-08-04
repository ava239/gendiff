<?php

namespace Gendiff\Formatters\Json;

function output($data, $depth = 0)
{
    return array_reduce(array_keys($data), function ($acc, $type) use ($data, $depth) {
        $operations = $data[$type];
        return array_merge($acc, outputType($operations, $type, $depth));
    }, []);
}

function outputType($data, $type, $depth)
{
    return array_reduce(array_keys($data), function ($acc, $key) use ($data, $depth, $type) {
        $value = $data[$key];
        if (is_array($value) && isset($value['kept'])) {
            return array_merge($acc, [
                $key => [
                    'value' => output($value, $depth + 1),
                    'type' => $type,
                ]
            ]);
        } else {
            if ($type === 'changed') {
                return array_merge($acc, [$key => ['value' => $value[1], 'old' => $value[0], 'type' => $type]]);
            } else {
                return array_merge($acc, [$key => ['value' => $value, 'type' => $type]]);
            }
        }
    }, []);
}
