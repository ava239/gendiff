<?php

namespace Gendiff\Formatters\Json;

function output($data)
{
    $iter = function ($elem, $current) use (&$iter, $data) {
        if (isset($elem['children'])) {
            $new = ['children' => array_reduce(
                $elem['children'],
                fn($acc, $i) => array_merge($acc, [$i['key'] => $iter($i, [])]),
                []
            )];
        } else {
            $new = ['value' => $elem['value'], 'type' => $elem['type']];
        }
        return array_merge($current, $new);
    };
    return array_reduce(
        $data,
        fn($acc, $elem) => array_merge($acc, [$elem['key'] => $iter($elem, [])]),
        []
    );
}
