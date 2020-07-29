<?php

namespace Gendiff\Output\Pretty;

function output($data)
{
    $result = "{\n";
    foreach ($data as $string) {
        $value = is_bool($string[1]) ? ($string[1] ? 'true' : 'false') : $string[1];
        $result .= "  {$string[2]} {$string[0]}: {$value}\n";
    }
    $result .= "}";
    return $result;
}
