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
        $this->code = LightnCandy::compile ($body, ["flags" =>
            LightnCandy::FLAG_BARE              // compile to function, that don't print rendered data to screen, but return it as a string
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

    private $code;
    private $renderer;
    private $vars = [];
 }