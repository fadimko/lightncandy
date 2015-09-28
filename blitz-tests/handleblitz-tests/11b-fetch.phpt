--TEST--
Blitz::fetch()
--FILE--
<?php
include ('common.inc');

$body1 =<<<BODY
{{ BEGIN test }}
Hello, {{ \$name }}!
{{ END }}
BODY;

$T = new Blitz();
$T->load($body1);
echo $T->fetch('test', array('name' => 'world'));
echo "\n";


class BlitzTest extends Blitz {
    public function getCode () {
        return $this->code;
    }
}

$body2 =<<<BODY
{{BEGIN film}}
Film: {{filmName}}
Cast:
    {{BEGIN actor}}
- {{actorName}} as
        {{BEGIN character}}
            {{characterName}}{{UNLESS _last}}, {{ELSE}};{{END}}
        {{END}}
    {{END}}
{{END}}
BODY;
$T = new BlitzTest();
$T->load($body2);
$vars =
['film' => [
    ['filmName' => 'The Naked Gun',
     'actor' => [
        ['actorName' => 'Leslie Nielsen',
        'character' => [
            ['characterName' => 'Frank Drebin']]
        ],
        ['actorName' => 'Tiny Ron',
        'character' => [
            ['characterName' => 'Al'],
            ['characterName' => 'Tall Lab Tech']]
        ]]
    ]]
];
echo $T->parse ($vars) . "\n";

echo $T->fetch ('film', $vars['film']) . "\n";

// original Blitz::fetch() corrupts array
$vars =
['film' => [
    ['filmName' => 'The Naked Gun',
     'actor' => [
        ['actorName' => 'Leslie Nielsen',
        'character' => [
            ['characterName' => 'Frank Drebin']]
        ],
        ['actorName' => 'Tiny Ron',
        'character' => [
            ['characterName' => 'Al'],
            ['characterName' => 'Tall Lab Tech']]
        ]]
    ]]
];
echo $T->fetch ('/film/actor', $vars['film'][0]['actor'][1]) . "\n";

$vars =
['film' => [
    ['filmName' => 'The Naked Gun',
     'actor' => [
        ['actorName' => 'Leslie Nielsen',
        'character' => [
            ['characterName' => 'Frank Drebin']]
        ],
        ['actorName' => 'Tiny Ron',
        'character' => [
            ['characterName' => 'Al'],
            ['characterName' => 'Tall Lab Tech']]
        ]]
    ]]
];
echo $T->fetch ('/film/actor/', $vars['film'][0]['actor'][1]) . "\n";

$vars =
['film' => [
    ['filmName' => 'The Naked Gun',
     'actor' => [
        ['actorName' => 'Leslie Nielsen',
        'character' => [
            ['characterName' => 'Frank Drebin']]
        ],
        ['actorName' => 'Tiny Ron',
        'character' => [
            ['characterName' => 'Al'],
            ['characterName' => 'Tall Lab Tech']]
        ]]
    ]]
];
echo $T->fetch ('film/actor', $vars['film'][0]['actor'][1]) . "\n";
?>
--EXPECT--
Hello, world!

Film: The Naked Gun
Cast:
- Leslie Nielsen as
            Frank Drebin;
- Tiny Ron as
            Al, 
            Tall Lab Tech;

Film: The Naked Gun
Cast:
- Leslie Nielsen as
            Frank Drebin;
- Tiny Ron as
            Al, 
            Tall Lab Tech;

- Tiny Ron as
            Al, 
            Tall Lab Tech;

- Tiny Ron as
            Al, 
            Tall Lab Tech;

- Tiny Ron as
            Al, 
            Tall Lab Tech;
