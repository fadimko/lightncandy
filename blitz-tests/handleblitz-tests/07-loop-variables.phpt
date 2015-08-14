--TEST--
Loop variables.
--FILE--
<?php
include ('common.inc');

$body = <<<BODY
{{ BEGIN list }}
==================================================
list. x = {{ \$x }}
  {{ BEGIN sublist }}
--------------------------------------------------
  _first = {{_first  }}
  \$_first = {{ \$_first }}
  _last = {{  _last}}
  \$_last = {{\$_last}}
  row.v = {{ \$v }}
  x = {{ \$x }}
  _num = {{_num}}
  \$_num = {{\$_num}}
  _NUM = {{_NUM}}
  _parent.x = {{ _parent.x }}
  \$_parent.parent.x = {{ \$_parent.parent.x }}
  _parent.parent.0 = {{ _parent.parent.0 }}
  {{ END }}
{{ END }}

BODY;
// Blitz doesn't allow this
//  _parent.'x' = {{ _parent.'x' }}
//  _parent.parent.'0' = {{ _parent.parent.'0' }}
//  _parent.parent[0] = {{ _parent.parent[0] }}

$T = new Blitz();
$T->load($body);

$data = array(
    'list' => array(
        array(
            'x' => 'data',
            'sublist' => array(
                array('v' => 'a'),
                array('v' => 'b'),
                array('v' => 'c'),
            ),
            'parent' => array (
                '0' => 'parent.0',
                'x' => 'parent.x',
            ),
        )
    )
);

$T->display ($data);
?>
--EXPECT--
==================================================
list. x = data
--------------------------------------------------
  _first = 1
  $_first = 1
  _last = 0
  $_last = 0
  row.v = a
  x = 
  _num = 1
  $_num = 1
  _NUM = 
  _parent.x = data
  $_parent.parent.x = parent.x
  _parent.parent.0 = 
--------------------------------------------------
  _first = 0
  $_first = 0
  _last = 0
  $_last = 0
  row.v = b
  x = 
  _num = 2
  $_num = 2
  _NUM = 
  _parent.x = data
  $_parent.parent.x = parent.x
  _parent.parent.0 = 
--------------------------------------------------
  _first = 0
  $_first = 0
  _last = 1
  $_last = 1
  row.v = c
  x = 
  _num = 3
  $_num = 3
  _NUM = 
  _parent.x = data
  $_parent.parent.x = parent.x
  _parent.parent.0 =
