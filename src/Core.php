<?php

namespace Gendiff\Core;

function compare($a, $b)
{
    $removed = array_filter(array_keys($a), fn($x) => !in_array($x, array_keys($b)));
    $added = array_filter(array_keys($b), fn($x) => !in_array($x, array_keys($a)));
    $changed1 = array_filter(array_keys($a), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed2 = array_filter(array_keys($b), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed = array_unique(array_merge($changed1, $changed2));
    $diff = array_reduce($changed, function ($acc, $i) use ($a, $b) {
        if ($a[$i] === $b[$i]) {
            $acc['kept'][$i] = $a[$i];
        } else {
            $acc['changed'][$i] = [$a[$i], $b[$i]];
        }
        return $acc;
    }, ['kept' => [], 'changed' => [], 'added' => [], 'removed' => []]);
    $diff = array_reduce($added, function ($acc, $i) use ($a, $b) {
        $acc['added'][$i] = $b[$i];
        return $acc;
    }, $diff);
    $diff = array_reduce($removed, function ($acc, $i) use ($a, $b) {
        $acc['removed'][$i] = $a[$i];
        return $acc;
    }, $diff);
    return $diff;
}
