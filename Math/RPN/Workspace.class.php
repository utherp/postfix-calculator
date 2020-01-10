<?php
namespace Math\RPN;
\define(__NAMESPACE__ . '\OPS', '+-%*/^=⋅×÷√');
\define(__NAMESPACE__ . '\DEFAULT_WORKSPACE_PATH', '~/.rpn.workspace.ser');

require_once('Exceptions.class.php');
require_once('Operation.class.php');

class Workspace { 

    // variable registers
    private $registers = [];
    // workspace RPN expressions
    public $expressions = [];

    // The constructor only accepts array reference for the variable registers
    function __construct (&$registers = false) {
        if ($registers !== false) $this->set_registers($registers);
    }

    // is_operator(op): determine if a character is an operator (or operand)
    static function is_operator($o = NULL) { return ((\iconv_strlen($o) === 1) && (\iconv_strpos(OPS, $o) !== false)); }

    // split a string into operators and operands
    static function parse ($str) { return \mb_split('\s+', $str); }

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
        foreach ($this->registers as $n => $reg) 
            $registers[$n] = $reg . '';
        return $registers;
    }

    // solve (or reduce) an expression
    public function solve($i) {
        return $this->expressions[$i] . ' ';
    }

    // set register space
    public function set_registers(&$registers) { $this->registers = &$registers; }

    // add registers to register space
    public function add_registers(&$registers) {
        foreach ($registers as $n => &$reg)
            $this->registers[$n] = &$reg;
    }

    // add an expression to workspace
    public function add_expression($pipeline) {
        $stack = [];
        if (!\is_array($pipeline)) $pipeline = static::parse($pipeline);
        $len = \count($pipeline);

        for ($i = 0; $i < $len; $i++) {
            if (!static::is_operator($pipeline[$i])) {
                \array_push($stack, $pipeline[$i]);
                continue;
            }

            $op2 = \array_pop($stack);
            $op1 = \array_pop($stack);
            $operation = new Operation($pipeline[$i], $op1, $op2, $this->registers);
            \array_push($stack, $operation);
        }
        // for well-formed RPN, there should only be *one* item in the stack... and Operation object
        if (count($stack) !== 1) throw new MalformedExpression('Incorrect operand count');

        $this->expressions[] = \array_pop($stack);
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

