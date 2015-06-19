--TEST--
Work with variables.
--FILE--
<?php
include ('common.inc');

$T = new Blitz ();
$T->load ('{{$var1}}');
$T->display ();
echo "\n";
$T->display (array ("var2" => "z"));
echo "\n";
$T->display (array ("var1" => "az"));
echo "\n";
$T->display (array ("var2" => "z"));
echo "\n";
$T->display (array ());
echo "\n";
echo "\n";

$T = new Blitz ();
$T->load ('azaz{{$var1}}{{ $var2 }}');
$T->display ();
echo "\n";
$T->display (array ());
echo "\n";
$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
echo "\n";

$T = new Blitz ();
$T->load ('azaz{{var1}}{{ var2 }}');
$T->display ();
echo "\n";
$T->display (array ());
echo "\n";
$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
echo "\n";

$T = new Blitz ();
$T->load ('azaz<!--$var1--><!-- $var2 -->');
$T->display ();
echo "\n";
$T->display (array ());
echo "\n";
$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
echo "\n";

$T = new Blitz ();
$T->load ('azaz<!--var1--><!-- var2 -->');
$T->display ();
echo "\n";
$T->display (array ());
echo "\n";
$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
echo "\n";

$T = new Blitz ();
$T->load ('azaz{{$var1}}{{ $var2 }}');
$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
$T->display (array ("var2" => "z"));
echo "\n";
$T->display (array ());
echo "\n";
$T->display ();
echo "\n";
echo "\n";
?>
--EXPECT--


az
az
az

azaz
azaz
azazza

azaz
azaz
azazza

azaz
azaz
azaza

azaz
azaz
azaza

azazza
azazzz
azazzz
azazzz

