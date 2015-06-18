<?php

include ('Blitz.php');

$pwd = dirname(__FILE__)."/";
ini_set('blitz.path', $pwd);

ini_set('blitz.warn_context_duplicates', 1);
ini_set('blitz.remove_spaces_around_context_tags', 1);
ini_set('blitz.disable_include', 1);

// set this handler to simplify error checking for both PHP4 and PHP5
error_reporting(E_ALL);
set_error_handler('default_error_handler');
function default_error_handler($errno, $errstr, $errfile, $errline) {
    $parts = preg_split('/(ERROR|WARNING)\:/',$errstr);
    if (!empty($parts[1])) {
        echo trim($parts[1])."\n";
    } else {
        echo $errstr."\n";
    }
}

?>
