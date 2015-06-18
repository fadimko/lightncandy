--TEST--
Existing of load().
--FILE--
<?php
include ('common.inc');

$T = new Blitz ();
$T->load ('meh');
?>
--EXPECT--

