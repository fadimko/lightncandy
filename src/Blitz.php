<?php
include "lightncandy.php";

class Blitz {

    /**
     * Load a template file.
     */
    function __construct ($filename = NULL) {
        if (isset ($filename))
            $this->load (file_get_contents ($filename));
    }

    /**
     * Load a template.
     *
     * @param string $body Blitz template body
     *
     * @return void
     */
    public function load ($body) {
        $preparedBody = $this->prepare ($body);

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
//            LightnCandy::FLAG_ERROR_LOG
        $this->renderer = eval ($this->code);
    }

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

    /**
     * Alias for setGlobals()
     */
    public function setGlobal ($vars) {
        $this->setGlobals ($vars);
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
        return $renderer ($vars, null, $this, $this->globals);
    }

    /**
     * Method, that converts Blitz templates to LightnCanduy.
     *
     * @param $template string Blitz template
     * @return string Handlebars template
     */
    protected function prepare ($template) {
        // before 5.6 PHP didn't allow to store arrays in consts
        if (Blitz::$tokensInitialized === false)
            Blitz::initializeBlitzHandlebarsTokens ();

        // Replace all <!-- --> tags with {{}}, and trim spaces inside tags
        $template = preg_replace (Blitz::$blitzTags, Blitz::$handlebarsTags, $template);

        // Throw the $s away. Throws away them even from strings.
        $template = preg_replace_callback (
            '/{{.*?}}/',
            function ($matches) {return str_replace('$', '', $matches [0]);},
            $template);

        // BEGIN, END, IF, block vars
        $template = preg_replace (Blitz::$blitzTokens, Blitz::$handlebarsTokens, $template);

        // Callbacks
        $template = preg_replace_callback (
            '/{{[^}]*?\\(.*?}}/',
            function ($matches) {
                $quoteCallback = preg_replace (
                    ['`{{([^ (]*?) *(\\( *(".*") *\\))}}`',
                     '`{{([^ (]*?) *(\\( *(\'.*\') *\\))}}`'],
                    ['{{<$1($3)}}',
                     '{{<$1($3)}}'],
                    $matches [0]);  // {{ foo ( "<smth>") }} -> {{<foo( "<smth>")}}
                if ($quoteCallback != $matches[0])
                    return $quoteCallback;

                preg_match ('/{{([^}]*?) *\\(([^)]*)\\)}}/', $matches [0], $callback);
                return "{{({$callback[1]} " . str_replace (',', ' ', $callback[2]) . '}}'; // {{ foo ( a ,b) }} -> {{(foo  a  b}}
            },
            $template);

        return $template;
    }

    private static function initializeBlitzHandlebarsTokens () {
        $id = '[a-zA-Z0-9_\.]+';    //ids + paths + numbers
        $expr = '[=><!]+';

        // contains pairs: Blitz token and matching Handlebars token
        $blitzHandlebarsTags = [
            ['`<!-- (.*?) -->`',        '{{$1}}'],
            ['`{{[ ]*(.*?)[ ]*}}`',     '{{$1}}'],
            ['`<!--[^-]*-->`',          '']    // Blitz treats "<!--" without space in the end as comments and doesn't show them
        ];

        $blitzHandlebarsTokens = [
            ['`{{(BEGIN|begin) +(.*?)}}`',      '{{#$2}}'],
            ['`{{(END|end)(| +(.*?))}}`',       '{{/$3}}'],

            ["`{{(IF|if) +($id)(| *($expr) *($id))}}`",             '{{#if $2 $4 $5}}'],
            ["`{{(UNLESS|unless) +($id)(| *($expr) *($id))}}`",     '{{#unless $2 $4 $5}}'],
            ["`{{ELSE}}`",       '{{else}}'],

            ['`{{(|[^}]* )_first(| [^}]*)}}`',      '{{$1@first$2}}'], // This regexps won't find block variables in function calls.
            ['`{{(|[^}]* )_last(| [^}]*)}}`',       '{{$1@last$2}}'],
            ['`{{(|[^}]* )_num(| [^}]*)}}`',        '{{$1@index$2}}'],
            ['`{{(|[^}]* )_parent\.(.*?)}}`',       '{{$1../$2}}']  // This will remove only the first occurrence of '_parent', which is fine for everything except callbacks
        ];

        Blitz::$blitzTags = array_map (function ($x) {return $x[0];}, $blitzHandlebarsTags);
        Blitz::$handlebarsTags = array_map (function ($x) {return $x[1];}, $blitzHandlebarsTags);
        Blitz::$blitzTokens = array_map (function ($x) {return $x[0];}, $blitzHandlebarsTokens);
        Blitz::$handlebarsTokens = array_map (function ($x) {return $x[1];}, $blitzHandlebarsTokens);
        Blitz::$tokensInitialized = true;
    }

    protected $code;
    protected $renderer;
    protected $vars = [];
    protected $globals = [];
    private static $tokensInitialized = false;
    private static $blitzTags = null;
    private static $handlebarsTags = null;
    private static $blitzTokens = null;
    private static $handlebarsTokens = null;
}