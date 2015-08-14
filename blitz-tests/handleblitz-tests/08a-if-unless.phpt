--TEST--
IF, UNLESS. Test from Blitz manual.
--FILE--
<?php
include ('common.inc');

$body = <<<BODY
{{ BEGIN list }}
==================================================
list #{{ \$_num }}, x = {{ \$x }}
{{ UNLESS sublist }}
   empty
{{ ELSE }}
--------------------------------------------------
{{ BEGIN sublist }}
  row #{{ _num }} v = {{ \$v }}, x = {{ \$x }}
{{ END }}
{{ END }}
{{ END }}
BODY;

$T = new Blitz();
$T->load($body);

$data = array(
    'list' => array(
        0 => array(
            'x' => 'first'
        ),
        1 => array(
            'x' => 'second',
            'sublist' => array(
                0 => array('v' => 'a'),
                1 => array('v' => 'b'),
            )
        )
    )
);

$T->display($data);
?>
--EXPECT--
==================================================
list #1, x = first
   empty
==================================================
list #2, x = second
--------------------------------------------------
  row #1 v = a, x = 
  row #2 v = b, x = 
