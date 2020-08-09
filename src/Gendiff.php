<?php

namespace Gendiff\Gendiff;

use Exception;
use Gendiff\Parsers;
use Gendiff\Core;
use Gendiff\Output;

function compareFiles($first, $second, $format = 'pretty')
{
    try {
        $object1 = Parsers\parse($first);
        $object2 = Parsers\parse($second);
        $diff = Core\compare($object1, $object2);
        return Output\format($diff, $format);
    } catch (Exception $e) {
        return "Error: {$e->getMessage()}";
    }
}
