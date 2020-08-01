<?php

namespace Gendiff\FormatParser;

function parse($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);
    }
    return null;
}
