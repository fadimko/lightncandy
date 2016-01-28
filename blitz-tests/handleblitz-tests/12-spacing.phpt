--TEST--
Spacing test.
--FILE--
<?php
include ('common.inc');
//  {{ IF b }} yep

$body = <<<BODY
A template string that consists only of spaces and 1 BEGIN/END/IF/UNLESS/ELSE tag is not printed.

A is defined.
{{ IF a }}
yep a
{{ ELSE }}
nope a
{{ END }}

  {{ IF a }}
yep a
  {{ ELSE }}
nope a
  {{ END }}

"  _\\n" is printed
  {{ IF a }}_
yep a
  {{ ELSE }}
nope a
  {{ END }}  

_ is in not inside IF, but it makes " _\\n" after "IF" to be printed.
 _{{ IF a }}
yep a
  {{ ELSE }}
nope a
  {{ END }}  

 {{ IF a }}yep a{{ ELSE }}nope a{{ END }}

B is undefined.
  {{ IF b }}
yep b
  {{ ELSE }}
nope b
  {{ END }}

_ is in the "true" branch, but it makes "\\n" after "ELSE" to be printed.
  {{ IF b }}
yep b
  _{{ ELSE }}
nope b
  {{ END }}

Strings with single variable/loop variable/functions call tag shouldn't be changed
{{ BEGIN block1 }}
    {{ v }}
    {{ _num }}
    {{ pow(10, v) }}
    {{ strtolower("STOP WRITING IN ALL CAPS!") }}
{{ END block1 }}

  {{ BEGIN block2 }}
    {{ IF _first }}
let's roll
    {{ END }}
    {{ IF _last }}
let's stop rolling
    {{ END }}
 {{ END block2 }}

2 "\\n"s (after each END) will be printed on every iteration
{{ BEGIN block3 }}
{{ IF _first }}let's roll{{ END }}
{{ IF _last }}let's stop rolling{{ END }}
{{ END block3 }}

There are 2 tags on the 1st lines, so all spaces and "\\n"s from this line will be printed as it is:
- 1 space will be added on every iteration
- on the first iteration (IF _first) will be printed additional "\\n"
{{ BEGIN block4 }} {{ IF _first }}
let's roll
    {{ END }}
    {{ IF _last }}
let's stop rolling
    {{ END }}
{{ END block4 }}

It is identical to block2, but it has no endofline in the last string
  {{ BEGIN block5 }}
    {{ IF _first }}
let's roll
    {{ END }}
    {{ IF _last }}
let's stop rolling
    {{ END }}
 {{ END block5 }}
BODY;

$T = new Blitz();
$T->load($body);

$data = array(
        'a' => 'a value',
        'block1' => [['v' => 3]],
        'block2' => [1,2,3,4],
        'block3' => [1,2,3,4],
        'block4' => [1,2,3,4],
        'block5' => [1,2,3,4],
        'g' => 57331
);

$T->display($data);
?>
--EXPECT--
A template string that consists only of spaces and 1 BEGIN/END/IF/UNLESS/ELSE tag is not printed.

A is defined.
yep a

yep a

"  _\n" is printed
  _
yep a

_ is in not inside IF, but it makes " _\n" after "IF" to be printed.
 _
yep a

 yep a

B is undefined.
nope b

_ is in the "true" branch, but it makes "\n" after "ELSE" to be printed.

nope b

Strings with single variable/loop variable/functions call tag shouldn't be changed
    3
    1
    1000
    stop writing in all caps!

let's roll
let's stop rolling

2 "\n"s (after each END) will be printed on every iteration
let's roll






let's stop rolling

There are 2 tags on the 1st lines, so all spaces and "\n"s from this line will be printed as it is:
- 1 space will be added on every iteration
- on the first iteration (IF _first) will be printed additional "\n"
 
let's roll
   let's stop rolling

It is identical to block2, but it has no endofline in the last string
let's roll
   let's stop rolling