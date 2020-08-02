<?php

namespace Gendiff\Output;

use Gendiff\Formatters\Pretty;
use Gendiff\Formatters\Plain;

function format($data, $format)
{
    switch ($format) {
        case 'pretty':
            return Pretty\output($data);
        case 'plain':
            return Plain\output($data);
    }
    return '';
}
