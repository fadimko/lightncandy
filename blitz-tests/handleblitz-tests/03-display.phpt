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
echo "\n\n";

echo $T->parse () . "\n";
echo $T->parse (array ()) . "\n";
echo $T->parse (array ("azaz" => "azazaza")) . "\n";
?>
--EXPECT--
azaz
azaz
azaz

azaz
azaz
azaz
