<?php

namespace Gendiff\Formatters;

use Exception;
use Gendiff\Formatters\Pretty;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;

const END_OF_LINE = "\n";

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

function formatBooleanValue(bool $value): string
{
    return $value ? 'true' : 'false';
}
