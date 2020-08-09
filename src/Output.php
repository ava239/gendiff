<?php

namespace Gendiff\Output;

use Error;
use Gendiff\Formatters\Pretty;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;

function format($data, $format)
{
    switch ($format) {
        case 'pretty':
            return joinLines(Pretty\output($data));
        case 'plain':
            return joinLines(Plain\output($data));
        case 'json':
            return json_encode(Json\output($data));
        default:
            throw new Error("Unknown format '$format}'");
    }
}

function joinLines($lines)
{
    return implode("\n", $lines);
}
