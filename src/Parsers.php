<?php

namespace Gendiff\Parsers;

use Error;
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
            throw new Error("Unsupported file extension '$format'");
    }
}
