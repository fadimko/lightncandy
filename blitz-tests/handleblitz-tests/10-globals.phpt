--TEST--
Globals.
--FILE--
<?php
include ('common.inc');

class BlitzTest extends Blitz {
    public function printCode () {
        echo $this->code . "\n\n";
    }

    public function printGlobals () {
        print_r ($this->globals);
        echo "\n\n";
    }
}

$body = '
It\'s like what {{ $local }} said...
It\'s like what {{ $global }} said...
{{ BEGIN context }}
    It\'s like what {{ $local }} said...
    It\'s like what {{ $global }} said...
{{ END context }}
';

$T = new BlitzTest();
$T->load($body);
$T->set([
    'local' => 'Lennon',
    'context' => [
        'local' => 'Lenin']
    ]);
$T->setGlobals(['global' => 'Lenin']);

//$T->printCode ();
//$T->printGlobals ();

$T->display();
?>
--EXPECT--
It's like what Lennon said...
It's like what Lenin said...
    It's like what Lenin said...
    It's like what Lenin said...