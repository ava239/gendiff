<?php

namespace Gendiff\Output;

use Error;
use Gendiff\Formatters\Pretty;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;

function format(array $data, string $format): string
{
    switch ($format) {
        case 'pretty':
            return implode("\n", Pretty\output($data));
        case 'plain':
            return implode("\n", Plain\output($data));
        case 'json':
            return json_encode(Json\output($data));
        default:
            throw new Error("Unknown format '$format'");
    }
}
