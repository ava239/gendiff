<?php

namespace Gendiff\Formatters;

use Exception;
use Gendiff\Formatters\Pretty;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;

function format(array $data, string $format): string
{
    switch ($format) {
        case 'pretty':
            return Pretty\format($data);
        case 'plain':
            return Plain\format($data);
        case 'json':
            return Json\format($data);
        default:
            throw new Exception("Unknown format '$format'");
    }
}

function formatValue($val): string
{
    if (is_bool($val)) {
        return $val ? 'true' : 'false';
    } else {
        return (string) $val;
    }
}
