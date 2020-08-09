<?php

namespace Gendiff\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse($filepath)
{
    $data = readFile($filepath);
    $format = detectFormat($filepath);
    switch ($format) {
        case 'json':
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        case 'yaml':
        case 'yml':
            $object = Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
            $object = convertToArray($object);
            return $object;
        default:
            throw new Exception("Unsupported file extension '$format'");
    }
}

function convertToArray($object)
{
    if (is_object($object)) {
        $object = (array)$object;
        $object = array_map('Gendiff\Parsers\convertToArray', $object);
    }
    return $object;
}

function readFile($file)
{
    $path = realpath($file);
    if (!is_file($path)) {
        throw new Exception("'$file' is not a file");
    }
    if (!is_readable($path)) {
        throw new Exception("Can`t read '$file'");
    }
    return file_get_contents($path);
}

function detectFormat($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}
