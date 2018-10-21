<?php
require_once ('parser.php');
$parser = new Parser;

$argument = $argv[1];//takes the first argument from command line

if (empty($argument)) {
    //die('No argument passed');
} else {
    print_r($parser->parse($argument));
}



