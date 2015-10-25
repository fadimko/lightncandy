<?php
include "lightncandy.php";

class Blitz {

    /**
     * Load a template.
     *
     * @param string $body Blitz template body
     *
     * @return void
     */
    public function load ($body) {
        $preparedBody = $this->prepare ($body);
//        echo $preparedBody . "\n\n";
        $this->code = LightnCandy::compile ($preparedBody, ["flags" =>
            LightnCandy::FLAG_BLITZ |
            LightnCandy::FLAG_BARE |        // compile to function, that don't print rendered data to screen, but return it as a string
            LightnCandy::FLAG_MUSTACHESP |  // remove spaces and linebreaks around context tags
            LightnCandy::FLAG_SPVARS |      // _num, _first, _last
            LightnCandy::FLAG_PARENT |      // _parent
            LightnCandy::FLAG_ELSE |        // enables else tag
            LightnCandy::FLAG_NOESCAPE |    // no html-escaping
            LightnCandy::FLAG_GLOBALS |
            LightnCandy::FLAG_ERROR_EXCEPTION
        ]);
        $this->renderer = eval ($this->code);
    }
//            LightnCandy::FLAG_ERROR_LOG

    /**
     * Render loaded template with data from $vars and print it.
     *
     * @param array() $vars Data to render the template.
     *
     * @return void
     */
    public function display ($vars = null) {
        echo $this->parse ($vars);
    }

    /**
     * Render loaded template with data from $vars and return it.
     *
     * @param array() $vars Data to render the template.
     *
     * @return void
     */
    public function parse ($vars = null) {
        if ($vars !== null)
            $this->set ($vars);
        $renderer = $this->renderer;    //PHP bug
        return $renderer($this->vars, null, $this, $this->globals);
    }

    /**
     * @param array() $vars Data to render the template.
     */
    public function set ($vars) {
        $this->vars = array_merge($this->vars, $vars);
    }

    /**
     * @param array() $vars Global data to render the template.
     */
    public function setGlobals ($vars) {
        $this->globals = $vars;
    }

    public function clean () {
        $this->vars = [];
    }

    public function getIterations () {
        return [$this->vars];
    }

    public function fetch ($path, $vars) {
        $path = trim ($path, '/');
        $lastSection = strrchr ($path, '/');
        $lastSection = empty ($lastSection) ? $path : substr ($lastSection, 1);
        $lightncandyPath = '\[' . str_replace('/', '\]\[', $path) . '\]';

        $blockStart = "\\/\\* BLITZBLOCK_{$lightncandyPath} \\*\\/";
        $blockEnd  = "\\/\\* !BLITZBLOCK_{$lightncandyPath} \\*\\/";
        $codeRegex = "/^(.*?return )'.*?{$blockStart}(.*){$blockEnd}/s";

        if (!preg_match ($codeRegex, $this->code, $matches)) {
            /*ERROR*/;
            return;
        }

        $code = $matches [1] . $matches [2] . ';};';
        $vars = [$lastSection => $vars];

        $renderer = eval ($code);
        //Возможно, понадобится array_merge
        return $renderer ($vars);
    }

    /**
     * Temporary method, that converts Blitz templates to Handlebars.
     *
     * @param $template string Blitz template
     * @return string Handlebars template
     */
    protected function prepare ($template) {
        // before 5.6 PHP didn't allow to store arrays in consts
        if (Blitz::$tokensInitialized === false)
            Blitz::initializeBlitzHandlebarsTokens ();

        $template = preg_replace (Blitz::$blitzTokens, Blitz::$handlebarsTokens, $template);
        $template = preg_replace_callback (

            //['/{{ *([^ (}]+) *(\\((\\\'|\\").*?[^\\]\\)) *}}/', '{{<$1$2}}'],   // {{foo ("<smth>" ) }} -> {{<foo("<smth>" ) }}

            //'/{{ *([^ (}]+) *((\\((?=\\").*?[^\\\\]\\"\\))|(\\((?=\\\').*?[^\\\\]\\\'\\))) *}}/'

            //'/{{(?=[^<]) *([^}]*?) *\\(([^)}]*)\\) *}}/',
            '/{{[^\'"}]*?\\(.*?}}/',
            function ($matches) {
                $quoteCallback = preg_replace (
                    '/{{ *([^ (}]+) *((\\((?=\\").*?[^\\\\]\\"\\))|(\\((?=\\\').*?[^\\\\]\\\'\\))) *}}/',
                    '{{<$1$2}}',
                    $matches [0]);  // {{ foo ("<smth>") }} -> {{<foo("<smth>")}}
                if ($quoteCallback != $matches[0])
                    return $quoteCallback;

                preg_match ('/{{ *([^}]*?) *\\(([^)}]*)\\) *}}/', $matches [0], $callback);
                return "{{({$callback[1]} " . str_replace ([',', '$'], ' ', $callback[2]) . '}}'; // {{ foo ( a ,b) }} -> {{(foo  a  b}}
            },
            $template);

        return $template;
    }

    private static function initializeBlitzHandlebarsTokens () {
        // contains pairs: Blitz token and matching Handlebars token
        $blitzHandlebarsTokens = [
            ['/({{|<!-- ) *\$?/', '{{'],
            ['/ *(}}| -->)/',     '}}'],
            ['/<!--[^-]*-->/',      ''],    // Blitz treats "<!--" without space in the end as comments and doesn't show them
            ['/{{(BEGIN|begin) *([^}]*)}}/',  '{{#$2}}'],
            ['/{{(END|end) *([^}]*)}}/',      '{{/$2}}'],
            ['/{{([^}]* |)\$?_(first|last)([^}]*)}}/',  '{{$1@$2$3}}'],
            ['/{{([^}]* |)\$?_num([^}]*)}}/',           '{{$1@index$2}}'],
            ['/{{([^}]* |)\$?_parent([^}]*)}}/',        '{{$1../$2}}'],
            ['/{{(IF|if) \$?([^}]*)}}/',            '{{#if $2}}'],
            ['/{{#if ([^}]*?)([<>=!]+)/',             '{{#if $1 $2 '],  // spaces around logic operators
            ['/{{(UNLESS|unless) \$?([^}]*)}}/',    '{{#unless $2}}'],
            ['/{{(ELSE|else)}}/',                   '{{else}}'],
            //['{{[ ]*[a-zA-Z0-9_]*\\((([ ,]*)([a-zA-Z0-9_\\-]*|[\\\'][^\\\']*[\\\']|[\\"][^\\"]*[\\"]))\\)[s]*}}']
            //['/({{#if[^}]*)==([^}]*}})/',     '$1 == $2']     // LightnCandy don't understand '==' without spaces
            //['/<!--[ ]+\$?/',   '{{'],
            //['/[ ]+-->/',       '}}'],
        ];

        Blitz::$blitzTokens = array_map (function ($x) {return $x[0];}, $blitzHandlebarsTokens);
        Blitz::$handlebarsTokens = array_map (function ($x) {return $x[1];}, $blitzHandlebarsTokens);
        Blitz::$tokensInitialized = true;
    }

    protected $code;
    protected $renderer;
    protected $vars = [];
    protected $globals = [];
    private static $tokensInitialized = false;
    private static $blitzTokens = null;
    private static $handlebarsTokens = null;
}