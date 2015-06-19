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
        $this->code = LightnCandy::compile ($preparedBody, ["flags" =>
            LightnCandy::FLAG_BLITZ |
            LightnCandy::FLAG_BARE |        // compile to function, that don't print rendered data to screen, but return it as a string
            LightnCandy::FLAG_MUSTACHESP    // remove spaces and linebreaks around context tags
        ]);
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
        if ($vars !== null)
            $this->vars = array_merge($this->vars, $vars);
        $renderer = $this->renderer;    //PHP bug
        echo $renderer($this->vars);
    }

    public function clean () {
        $this->vars = [];
    }

    public function getIterations () {
        return [$this->vars];
    }

    /**
     * Temporary method, that converts Blitz templates to Handlebars.
     *
     * @param $template string Blitz template
     * @return string Handlebars template
     */
    private function prepare ($template) {
        // before 5.6 PHP didn't allow to store arrays in consts
        if (Blitz::$tokensInitialized === false)
            Blitz::initializeBlitzHandlebarsTokens ();

        return preg_replace (Blitz::$blitzTokens, Blitz::$handlebarsTokens, $template);
    }

    private static function initializeBlitzHandlebarsTokens () {
        // contains pairs: Blitz token and matching Handlebars token
        $blitzHandlebarsTokens = [
            ['/([^{]?){{[ ]*\$?/',  '$1{{{'],    // '{{{' and '}}}' means no html-escaping
            ['/<!--[ ]+\$?/',       '{{{'],
            ['/[ ]*}}/',            '}}}'],
            ['/[ ]+-->/',           '}}}'],
            ['/<!--[^-]*-->/',      ''],  // Blitz treats "<!--" without space in the end as comments and doesn't show them
            ['/{{{(BEGIN|begin)[ ]*([^}]*)}}}/', '{{#$2}}'],    // kinda workaround: we replaced all '{{' with '{{{' because of
            ['/{{{(END|end)[ ]*([^}]*)}}}/', '{{/$2}}'],        // html-escaping that we don't need, but blocks must be in '{{'
        ];                                                      // so we remove superfluous braces

        Blitz::$blitzTokens = array_map (function ($x) {return $x[0];}, $blitzHandlebarsTokens);
        Blitz::$handlebarsTokens = array_map (function ($x) {return $x[1];}, $blitzHandlebarsTokens);
        Blitz::$tokensInitialized = true;
    }

    private $code;
    private $renderer;
    private $vars = [];
    private static $tokensInitialized = false;
    private static $blitzTokens = null;
    private static $handlebarsTokens = null;
}