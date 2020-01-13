<?php
namespace Math\RPN;

require_once('Exceptions.class.php');
require_once('Operation.class.php');

class Workspace { 

    // variable registers
    public $registry = NULL;
    // workspace RPN expressions
    public $expressions = [];

    // The constructor only accepts array reference for the variable registers
    function __construct (&$registry = NULL) {
        if ($registry === NULL) return;
        if (!($registry instanceof Registry)) $registry = new Registry($registry);
        $this->registry = &$registry;
    }

    // dump all expressions to the output
    public function dump_expressions () {
        $expressions = [];
        foreach ($this->expressions as $expr)
            $expressions[] = $expr->express();
        return $expressions;
    }

    // dump all reduced expressions to output
    public function dump_reductions () {
        $reduced = [];
        foreach ($this->expressions as $expr) {
            $r = $expr->reduce();
            $reduced[] = \is_array($r) ? \implode(' ', $r) : $r;
        }
        return $reduced;
    }

    // dump all registers to output
    public function dump_registers () {
        $registers = [];
        foreach ($this->registry as $n => $reg) 
            $registers[$n] = $reg . '';
        return $registers;
    }

    // solve (or reduce) an expression
    public function solve($i) {
        return $this->expressions[$i] . '';
    }

    // add an expression to workspace
    public function add_expression($pipeline) {
        return $this->expressions[] = new Expression($pipeline, $this);
    }

    // save workspace
    public function save_workspace($path = false) {
        if (!$path) $path = DEFAULT_WORKSPACE_PATH;
        \file_put_contents($path, \serialize($this));
    }

    // load workspace
    static function load_workspace($path = false) {
        if (!$path) $path = DEFAULT_WORKSPACE_PATH;
        if (!\file_exists($path)) return NULL;
        return \unserialize(\file_get_contents($path));
    }
}

