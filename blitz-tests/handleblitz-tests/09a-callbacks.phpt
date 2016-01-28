--TEST--
Callbacks.
--FILE--
<?php
include ('common.inc');

// Now BlitznCandy supports two types of callbacks:
// 1. {{ foo ($arg1, $arg2, 12, ...) }} - any (including zero) number of variables and numbers as arguments
// 2. {{ bar ("some text") }}           - only one string argument
// Like in Blitz you can:
// - Extend Blitz and use your custom template methods in the template code
// - Call any other PHP function like do($something)
// Differences:
// - Blitz don't understand {{ foo () }} (I think, it tries to find function "foo "), and BlitznCandy does

function getNumber () {
    return 17;
}
function getString ($number) {
    return ($number == 23) ? "good string :>" : "bad string :<";
}
function getSum ($number1, $number2) {
    return $number1 + $number2;
}
function beautifyString ($s) {
    return '\o/ ' . $s . ' \o/';
}

class BlitzSubclass extends Blitz {
    // should never be called, because function with the same name defined already
    public function getNumber () {
        return 18;
    }

    public function subclassFunction () {
        return "Yay, Ima subclass function!";
    }
}

$template = <<<TEMPLATE
{{ getNumber() }}
{{ getString(23) }}
{{ getSum(\$num1, 10) }}
{{ getSum(num1, 10) }}
{{ getSum(10, num2) }}
{{ getSum(\$num1, \$num2) }}
{{ beautifyString("ugly string") }}
{{ beautifyString( "ugly string"  ) }}
{{ beautifyString( 'ugly string'  ) }}
{{ subclassFunction() }}
{{ substr(\$data, 3) }}


TEMPLATE;

$b = new BlitzSubclass;
$b->load ($template);
$vars = [
    'num1' => 11,
    'num2' => 12,
    'data' => 'sausage',
    ];
$b->display ($vars);

// Check, that cbObj is passed correctly to block's anonymous function.

/*
*/

$template = <<<TEMPLATE
{{BEGIN block}}
    {{ getNumber() }}
    {{ getString(23) }}
    {{ getSum(\$num1, 10) }}
    {{ getSum(num1, 10) }}
    {{ getSum(10, num2) }}
    {{ getSum( \$num1, \$num2) }}
    {{ beautifyString("ugly string") }}
    {{ beautifyString( "ugly string"  ) }}
    {{ beautifyString( 'ugly string'  ) }}
    {{ subclassFunction() }}
    {{ substr(\$data, 3) }}
{{END}}

{{IF block}}
    {{ getNumber() }}
    {{ getString(23) }}
    {{ getSum(\$num1, 10) }}
    {{ getSum(num1, 10) }}
    {{ getSum(10, num2) }}
    {{ getSum( \$num1, \$num2  ) }}
    {{ beautifyString("ugly string") }}
    {{ beautifyString( "ugly string"  ) }}
    {{ beautifyString( 'ugly string'  ) }}
    {{ subclassFunction() }}
    {{ substr(\$data, 3) }}
{{END}}


TEMPLATE;

$b = new BlitzSubclass;
$b->load ($template);
$vars = [
    'block' =>[
        ['num1' => 11,
        'num2' => 12,
        'data' => 'sausage']
    ],
    'num1' => 11,
    'num2' => 12,
    'data' => 'sausage'
    ];
$b->display ($vars);

?>
--EXPECT--
17
good string :>
21
21
22
23
\o/ ugly string \o/
\o/ ugly string \o/
\o/ ugly string \o/
Yay, Ima subclass function!
sage

    17
    good string :>
    21
    21
    22
    23
    \o/ ugly string \o/
    \o/ ugly string \o/
    \o/ ugly string \o/
    Yay, Ima subclass function!
    sage

    17
    good string :>
    21
    21
    22
    23
    \o/ ugly string \o/
    \o/ ugly string \o/
    \o/ ugly string \o/
    Yay, Ima subclass function!
    sage
