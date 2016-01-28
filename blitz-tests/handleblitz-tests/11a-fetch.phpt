--TEST--
Blitz::fetch(). check code genetaion
--FILE--
<?php
include ('common.inc');

class BlitzTest extends Blitz {
    public function getCode () {
        return $this->code;
    }
}

$body =<<<BODY
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
$T->load ($body);
echo $T->getCode ();
?>
--EXPECT--
return function ($in, $debugopt = 1, $cbObj = NULL, $globals = NULL) {
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => true,
            'prop' => false,
            'method' => false,
            'mustlok' => false,
            'echo' => false,
            'debug' => $debugopt,
            'blitz' => true,
        ),
        'constants' => array(),
        'helpers' => array(),
        'blockhelpers' => array(),
        'hbhelpers' => array(),
        'partials' => array(),
        'scopes' => array(),
        'sp_vars' => array('root' => $in),
        'lcrun' => 'LCRun3',
        'cbObj' => $cbObj,
        'globals' => $globals,

    );
    
    return ''./* BLITZBLOCK_[film] */LCRun3::sec($cx, ((isset($in['film']) && is_array($in)) ? $in['film'] : ((isset($cx['globals']['film']) && is_array($cx['globals'])) ? $cx['globals']['film'] : null)), $in, false, function($cx, $in) {return 'Film: '.((isset($in['filmName']) && is_array($in)) ? $in['filmName'] : ((isset($cx['globals']['filmName']) && is_array($cx['globals'])) ? $cx['globals']['filmName'] : null)).'
Cast:
'./* BLITZBLOCK_[film][actor] */LCRun3::sec($cx, ((isset($in['actor']) && is_array($in)) ? $in['actor'] : ((isset($cx['globals']['actor']) && is_array($cx['globals'])) ? $cx['globals']['actor'] : null)), $in, false, function($cx, $in) {return '- '.((isset($in['actorName']) && is_array($in)) ? $in['actorName'] : ((isset($cx['globals']['actorName']) && is_array($cx['globals'])) ? $cx['globals']['actorName'] : null)).' as
'./* BLITZBLOCK_[film][actor][character] */LCRun3::sec($cx, ((isset($in['character']) && is_array($in)) ? $in['character'] : ((isset($cx['globals']['character']) && is_array($cx['globals'])) ? $cx['globals']['character'] : null)), $in, false, function($cx, $in) {return '            '.((isset($in['characterName']) && is_array($in)) ? $in['characterName'] : ((isset($cx['globals']['characterName']) && is_array($cx['globals'])) ? $cx['globals']['characterName'] : null)).''.((!LCRun3::ifvar($cx, ((isset($cx['sp_vars']['last']) && is_array($cx['sp_vars'])) ? $cx['sp_vars']['last'] : ((isset($cx['globals']['last']) && is_array($cx['globals'])) ? $cx['globals']['last'] : null)))) ? ', ' : ';').'
';})/* !BLITZBLOCK_[film][actor][character] */.'';})/* !BLITZBLOCK_[film][actor] */.'';})/* !BLITZBLOCK_[film] */.'';
};