--TEST--
IF, UNLESS. Simple existance checks, arithmetical comparison.
--FILE--
<?php
include ('common.inc');

$body = <<<BODY
{{ IF a }}
yep a
{{ ELSE }}
nope a
{{ END }}
{{ UNLESS a }}
nope a
{{ ELSE }}
yep a
{{ END }}

{{ if b }}
yep b
{{ else }}
nope b
{{ end }}
{{ unless b }}
nope b
{{ else }}
yep b
{{ end }}

{{IF \$c}}
yep c
{{END}}
{{UNLESS \$c}}
nope c
{{END}}

{{IF d==true}}
yep d
{{ELSE}}
nope d
{{END}}
{{IF d == true}}
yep d
{{ELSE}}
nope d
{{END}}
{{IF d == false}}
nope d
{{ELSE}}
yep d
{{END}}

{{ IF e==10 }}
yep e == 10
{{ELSE}}
nope e == {{e}}
{{END}}
{{ IF f == 10 }}
yep f == 10
{{ELSE}}
nope f == {{f}}
{{END}}

{{ IF e == e1 }}
yep e == e1
{{ ELSE }}
nope e != e1
{{ END }}
{{ IF e == f }}
yep e == f
{{ ELSE }}
nope e != f
{{ END }}

{{ BEGIN block }}
  {{ IF _first }}
let's roll
  {{ END }}
{{\$_num}}{{ IF _num == 2 }}!!{{END}}{{ IF \$_num==3 }}!!!{{END}}
  {{ IF \$_last }}
let's stop rolling
  {{ END }}
{{ END }}

{{ IF g }}
you can write one word after END in tag, that closes if.
{{ END nobody_cares_what_you_wrote_here }}
{{ IF h }}
and nobody cares what you will write,
{{ ELSE }}
and nobody cares what you will write.
{{ END still_nobody_cares }}
BODY;

$T = new Blitz();
$T->load($body);

$data = array(
    'a' => [1],
    'c' => [1],
    'd' => [1],
    'e' => 1338,
    'f' => 10,
    'e1' => 1338,
    'block' => [1,2,3,4],
    'g' => 57331
);

$T->display($data);
?>
--EXPECT--
yep a
yep a

nope b
nope b

yep c

yep d
yep d
yep d

nope e == 1338
yep f == 10

yep e == e1
nope e != f

let's roll
1
2!!
3!!!
4
let's stop rolling

you can write one word after END in tag, that closes if.
and nobody cares what you will write.