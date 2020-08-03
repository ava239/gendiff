<?php

namespace Gendiff\Gendiff;

use Gendiff\Parsers;
use Gendiff\Core;
use Gendiff\Output;
use Docopt;
use function cli\line;

function run()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]
DOC;
    $args = Docopt::handle($doc);

    line(compareFiles($args['<firstFile>'], $args['<secondFile>'], $args['--format']));
}

function compareFiles($first, $second, $format = 'pretty')
{
    $file1 = readFile($first);
    $file2 = readFile($second);
    $format1 = detectFormat($first);
    $format2 = detectFormat($second);
    $object1 = Parsers\parse($file1, $format1);
    $object2 = Parsers\parse($file2, $format2);
    $diff = Core\compare($object1, $object2);
    return Output\format($diff, $format);
}

function readFile($file)
{
    $path = realpath($file);
    return file_get_contents($path);
}

function detectFormat($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}
