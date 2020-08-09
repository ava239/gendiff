<?php

namespace Gendiff\Core;

function compare($a, $b)
{
    $removed = array_filter(array_keys($a), fn($x) => !in_array($x, array_keys($b)));
    $added = array_filter(array_keys($b), fn($x) => !in_array($x, array_keys($a)));
    $changed1 = array_filter(array_keys($a), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed2 = array_filter(array_keys($b), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed = array_unique(array_merge($changed1, $changed2));
    $innerCompare = function ($a, $b) {
        if (is_array($a) && is_array($b)) {
            return compare($a, $b);
        } else {
            return $a;
        }
    };
    $diff = array_reduce($changed, function ($acc, $i) use ($a, $b, $innerCompare) {
        if (is_array($a[$i]) || $a[$i] === $b[$i]) {
            $acc['kept'][$i] = $innerCompare($a[$i], $b[$i]);
        } else {
            $acc['changed'][$i] = [$innerCompare($a[$i], $b[$i]), $innerCompare($b[$i], $a[$i])];
        }
        return $acc;
    }, ['kept' => [], 'changed' => [], 'removed' => [], 'added' => []]);
    $diff = array_reduce($added, function ($acc, $i) use ($a, $b, $innerCompare) {
        $acc['added'][$i] = $innerCompare($b[$i], $b[$i]);
        return $acc;
    }, $diff);
    $diff = array_reduce($removed, function ($acc, $i) use ($a, $b, $innerCompare) {
        $acc['removed'][$i] = $innerCompare($a[$i], $a[$i]);
        return $acc;
    }, $diff);
    return $diff;
}
