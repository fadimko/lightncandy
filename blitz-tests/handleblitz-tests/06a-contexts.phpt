--TEST--
Work with variables.
--FILE--
<?php
include ('common.inc');

$body = <<<BODY
{{ BEGIN list }}
==================================================
list. x = {{ \$x }}
--------------------------------------------------
 {{ BEGIN sublist }}  
  row. v = {{ \$v }}, x = {{ \$x }}
   {{ END }}    
     {{ END }}

BODY;

$T = new Blitz();
$T->load($body);
    
$data = array(
    'list' => array(
        array(
            'x' => 'first'
        ),
        array(
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
list. x = first
--------------------------------------------------
==================================================
list. x = second
--------------------------------------------------
  row. v = a, x = 
  row. v = b, x = 
