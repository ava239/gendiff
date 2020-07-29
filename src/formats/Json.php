<?php

namespace Gendiff\Formats\Json;

use Gendiff\Gendiff;

function compare($a, $b)
{
    $removed = array_filter(array_keys($a), fn($x) => !in_array($x, array_keys($b)));
    $added = array_filter(array_keys($b), fn($x) => !in_array($x, array_keys($a)));
    $changed1 = array_filter(array_keys($a), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed2 = array_filter(array_keys($b), fn($x) => !in_array($x, $added) && !in_array($x, $removed));
    $changed = array_unique(array_merge($changed1, $changed2));
    $output = array_reduce($changed, function ($acc, $i) use ($a, $b) {
        if ($a[$i] === $b[$i]) {
            return [...$acc, [$i, $a[$i], ' ']];
        }
        return [...$acc, [$i, $a[$i], '-'], [$i, $b[$i], '+']];
    }, []);
    $output = array_reduce($added, function ($acc, $i) use ($a, $b) {
        return [...$acc, [$i, $b[$i], '+']];
    }, $output);
    $output = array_reduce($removed, function ($acc, $i) use ($a, $b) {
        return [...$acc, [$i, $a[$i], '-']];
    }, $output);
    return Gendiff\printResult($output, 'pretty');
}
