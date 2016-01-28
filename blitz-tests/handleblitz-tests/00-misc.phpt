--TEST--
misc.
--FILE--
<?php
include ('common.inc');

class BlitzTest extends Blitz {
    public function testPrepare ($body) {
        return parent::prepare ($body);
    }
}

$b = new BlitzTest ();
echo $b->testPrepare ('{{_first}}{{IF _num == _parent.x}}{{BEGIN nothing}}{{END}}') . "\n";
echo $b->testPrepare ('{{_firstaaa}}{{IF _num111 == _parent___.x}}{{BEGIN___ nothing}}{{END111}}'). "\n";
?>
--EXPECT--
{{@first}}{{#if @index == ../x}}{{#nothing}}{{/}}
{{_firstaaa}}{{#if _num111 == _parent___.x}}{{BEGIN___ nothing}}{{END111}}
