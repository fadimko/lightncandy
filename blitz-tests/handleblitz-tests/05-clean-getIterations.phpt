--TEST--
clean() and getIterations() methods
--FILE--
<?php
include ('common.inc');

$T = new Blitz ();
$T->load ('azaz{{$var1}}{{ $var2 }}');

$T->display (array ("var1" => "z", "var2" => "a"));
echo "\n";
var_dump ($T->getIterations ());

$T->display (array ("var2" => "z"));
echo "\n";
var_dump ($T->getIterations ());

$T->clean ();
$T->display ();
echo "\n";
var_dump ($T->getIterations ());
?>
--EXPECT--
azazza
array(1) {
  [0]=>
  array(2) {
    ["var1"]=>
    string(1) "z"
    ["var2"]=>
    string(1) "a"
  }
}
azazzz
array(1) {
  [0]=>
  array(2) {
    ["var1"]=>
    string(1) "z"
    ["var2"]=>
    string(1) "z"
  }
}
azaz
array(1) {
  [0]=>
  array(0) {
  }
}
