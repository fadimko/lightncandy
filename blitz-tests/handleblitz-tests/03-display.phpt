--TEST--
Displaying simple text.
--FILE--
<?php
include ('common.inc');

$T = new Blitz ();
$T->load ('azaz');
$T->display ();
echo "\n";
$T->display (array ());
echo "\n";
$T->display (array ("azaz" => "azazaza"));
?>
--EXPECT--
azaz
azaz
azaz