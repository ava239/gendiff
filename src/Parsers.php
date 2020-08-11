<?php

namespace Gendiff\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $format): array
{
    switch ($format) {
        case 'json':
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        case 'yaml':
        case 'yml':
            return Yaml::parse($data);
        default:
            throw new Exception("Unsupported file extension '$format'");
    }
}
