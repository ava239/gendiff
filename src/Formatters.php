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

function formatValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_array($value)) {
        return 'complex value';
    } else {
        return (string) $value;
    }
}
