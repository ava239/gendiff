<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);
        case 'yml':
            $object = Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
            $object = convertToArray($object);
            return $object;
    }
    return [];
}

function convertToArray($object)
{
    if (is_object($object)) {
        $object = (array) $object;
        foreach ($object as $key => $val) {
            $object[$key] = convertToArray($val);
        }
    }
    return $object;
}
