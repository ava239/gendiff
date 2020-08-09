<?php

namespace Gendiff\Parsers;

use Error;
use Symfony\Component\Yaml\Yaml;

function parse($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);
        case 'yaml':
        case 'yml':
            $object = Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
            $object = convertToArray($object);
            return $object;
        default:
            throw new Error("Unsupported file extension '$format'");
    }
}

function convertToArray($object)
{
    if (is_object($object)) {
        $object = (array) $object;
        $object = array_map('Gendiff\Parsers\convertToArray', $object);
    }
    return $object;
}
