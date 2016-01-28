--TEST--
Callbacks. Test blitz->lightncandy parser.
--FILE--
<?php
include ('common.inc');

$template = <<<TEMPLATE
 {{foo()}} {{  asdf      (  efef, ee, 0                )}} {{ffuu ()}}  {{ ffuuuuu( f1, f2   ,ff3 , f4) }}
 {{foo( "" )}} {{  asdf      ( '  efef, ee, 0               ')}} {{ffuu ("")}}  {{ ffuuuuu(" f1, f2   ,ff3 , f4") }}
TEMPLATE;

class BlitzTest extends Blitz {
    public function checkPrepare ($template) {
        return parent::prepare ($template);
    }
}

$b = new BlitzTest;
echo $b->checkPrepare ($template);
?>
--EXPECT--
{{(foo }} {{(asdf   efef  ee  0                }} {{(ffuu }}  {{(ffuuuuu  f1  f2    ff3   f4}}
 {{<foo("")}} {{<asdf('  efef, ee, 0               ')}} {{<ffuu("")}}  {{<ffuuuuu(" f1, f2   ,ff3 , f4")}}
