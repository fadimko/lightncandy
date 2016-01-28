--TEST--
Work with contexts.
--FILE--
<?php
include ('common.inc');

$bl = new Blitz;
$bl->load ('hello_{{ BEGIN block }}_{{ $name }}_{{ END block }}');
$vars = array ();
$vars ['block'] = array ();
foreach (array ('Dude', 'Donny', 'Sobchak') as $i_name) {
    $vars ['block'][] = array ('name' => $i_name);
}
$bl->display ($vars);
echo "\n";

/* Blitz understands only upper case and lower case, no mixed case */
$bl = new Blitz;
$bl->load ('hello {{BEGIN b1}}{{ $name }}{{END b1}}, {{begin b2}}{{ $name }}{{END b2}}, {{BEGIN b3}}{{ $name }}{{end b3}}, {{begin b4}}{{ $name }}{{end b4}}');
$vars = array ();
$vars ['b1'] = array ('name' => 'Dude');
$vars ['b2'] = array ('name' => 'Donny');
$vars ['b3'] = array ('name' => 'Sobchak');
$vars ['b4'] = array ('name' => 'Jesus');
$bl->display ($vars);
echo "\n";

/* Blitz understands end tags without block name */
$bl = new Blitz;
$bl->load ('{{BEGIN b1}}hello Dude, Donny, Sobchak, Jesus{{END}}');
$vars = array ();
$vars ['b1'] = array ('name' => 'Donny');
$bl->display ($vars);
echo "\n";

$bl = new Blitz;
$bl->load ('hello {{BEGIN B1}}Dude{{END}}, {{BEGIN b2}}Donny{{END}}, {{BEGIN b3}}Sobchak{{END}}, {{BEGIN b4}}Jesus{{END}}, {{BEGIN b5}}Maude Lebowski{{END}}, {{BEGIN b6}}The Big Lebowski{{END}}');
$vars = array ();
$vars ['b1'] = NULL;
$vars ['b2'] = "";
$vars ['b3'] = true;
$vars ['b4'] = [];
$vars ['b5'] = [1, 0, 3];
$vars ['b6'] = ['carpet'];
$bl->display ($vars);
echo "\n";

/* Blitz accepts array of values if it is an array and it's first element has numeric key */
$bl = new Blitz;
$bl->load ('{{ BEGIN resizers }}
server {{ hostname }}:{{ port }};
{{ END }}');
$bl->display (['resizers' => [
    'abc' => ['hostname' => '127.0.0.1', 'port' => 1337],
    ]]);
echo "\n";

$bl->display (['resizers' => [
    1234 => ['hostname' => '127.0.0.1', 'port' => 1337],
    'abc' => ['hostname' => '127.0.0.2', 'port' => 1338],
    ]]);
echo "\n";

$bl->display (['resizers' => [
    'abc' => ['hostname' => '127.0.0.2', 'port' => 1338],
    1234 => ['hostname' => '127.0.0.1', 'port' => 1337],
    ]]);
echo "\n";
?>
--EXPECT--
hello__Dude__Donny__Sobchak_
hello Dude, Donny, Sobchak, Jesus
hello Dude, Donny, Sobchak, Jesus
hello , , , , Maude LebowskiMaude LebowskiMaude Lebowski, The Big Lebowski
server :;

server 127.0.0.1:1337;
server 127.0.0.2:1338;

server :;
