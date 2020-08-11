<?php

namespace Gendiff\Output;

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
