<?php

namespace Gendiff\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $format): object
{
    switch ($format) {
        case 'json':
            return json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        case 'yaml':
        case 'yml':
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new Exception("Unsupported file extension '$format'");
    }
}
