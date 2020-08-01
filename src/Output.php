<?php

namespace Gendiff\Output;

use Gendiff\Output\Pretty;

function format($data, $format)
{
    switch ($format) {
        case 'pretty':
            return Pretty\output($data);
    }
    return null;
}
