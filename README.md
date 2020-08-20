# Gendiff CLI utility
![CI](https://github.com/ava239/php-project-lvl2/workflows/CI/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/25ef186196e3546e1b1a/maintainability)](https://codeclimate.com/github/ava239/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/25ef186196e3546e1b1a/test_coverage)](https://codeclimate.com/github/ava239/php-project-lvl2/test_coverage)

## Description
Utility can generate diff between two files  
Can be used as standalone CLI utility or as a library  
Supports absolute and relative paths to files  
File formats supported:  
- json
- yaml

Output formats supported (see below for examples):
- pretty
- plain
- json

## Requirements
- PHP 7.4
- ext-json extension installed

## Installation
### Standalone CLI utility
Global installation with composer recomended
``` sh
$ composer g require ava/gendiff
```
[![asciicast](https://asciinema.org/a/352365.svg)](https://asciinema.org/a/352365)
Then you can run it globally (if you have global composer dir in your $PATH) as CLI program

Usage format is:  
``` sh
$ gendiff [--format <fmt>] <firstFile> <secondFile>
```
You can always get usage help with
``` sh
$ gendiff -h
```
There are usage examples in section below

### Library
Composer is recomended to install
``` sh
$ composer require ava/gendiff
```
Then you can use it in your project
``` PHP
use function Gendiff\Core\compareFiles;
...
$diff = compareFiles($filepath1, $filepath2);
```
This will return diff between files in pretty format  
You also can pass 3rd argument to change format
Formats supported:  
- pretty
- plain
- json

Below you can find examples how different formats looks

## Usage examples (CLI version)
pretty print format
[![asciicast](https://asciinema.org/a/351109.svg)](https://asciinema.org/a/351109)

plain format
[![asciicast](https://asciinema.org/a/351261.svg)](https://asciinema.org/a/351261)

json format output
[![asciicast](https://asciinema.org/a/351467.svg)](https://asciinema.org/a/351467)