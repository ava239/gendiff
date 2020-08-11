<?php

namespace Gendiff\Formatters\Json;

function format(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT);
}
