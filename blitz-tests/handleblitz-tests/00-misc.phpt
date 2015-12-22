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
echo $b->testPrepare ('{{_first}}{{IF _num == _parent}}{{BEGIN}}{{END}}') . "\n";
echo $b->testPrepare ('{{_firstaaa}}{{IF _num111 == _parent___}}{{BEGIN___}}{{END111}}'). "\n";
?>
--EXPECT--
{{@first}}{{#if @index  ==  ../}}{{#}}{{/}}
{{_firstaaa}}{{#if _num111  ==  _parent___}}{{BEGIN___}}{{END111}}
