<?php
namespace Math\RPN;

require_once('Exceptions.class.php');
require_once('Operation.class.php');

class Expression implements \ArrayAccess {

    // global workspace of expressions and registers
    private $workspace = NULL;
    // all parts of the expression (technically, all operands and operations)
    private $operands = [];
    // processed parts into Operations
    private $stack = [];
    // whether all the operands have been processed onto the stack
    private $processed = false;

    function __construct ($operands = [], &$workspace = NULL) {
        $this->workspace = &$workspace;
        $this[] = $operands;
    }

    private static function parse_items  ($operands) {
        $output = [];
        if (!is_array($operands)) $operands = [ $operands ];
        foreach ($operands as $op) {
            $op = is_array($op) ? self::parse_items($op) : parse($op);
            $output = array_merge($output, $op);
        }
        return $output;
    }

    function offsetExists ($i)     { return key_exists($i, $this->operands); }
    function offsetGet    ($i)     { return $this->operands[$i]; }
    function offsetUnset  ($i)     { array_splice($this->operands, $i, 1); $this->processed = false; } // here we'll just remove the slice
    function offsetSet    ($i, $v) { 
        if ($i === NULL) $i = count($this->operands);
        $v = self::parse_items($v);
        array_splice($this->operands, $i, 1, $v);
        $this->processed = false;
    }

    function __toString     () { return implode(' ', $this->reduce()); }
    function express        () { return implode(' ', $this->operands); }
    function reduce         () { 
        $this->process();
        return $this->iterate_operations('reduce'); 
    }

    private function iterate_operations ($method) {
        $parts = []; $len = count($this->stack);
        for ($i = 0; $i < $len; $i++) {
            $part = $this->stack[$i];
            if ($part instanceof Operation)
                $parts = array_merge($parts, $part->$method());
            else $parts[] = $part;
        }
        return $parts;
    }

    function process() {
        if ($this->processed) return;
        $len = count($this->operands);
        $this->stack = [];
        for ($i = 0; $i < $len; $i++) {
            $arg = $this->operands[$i];
            if (!is_operator($arg)) {
                \array_push($this->stack, $arg);
                continue;
            }

            if (count($this->stack) < 2) throw new InvalidUnaryOperation('Operator must be preceeded by at least two operands');
            $op2 = \array_pop($this->stack);
            $op1 = \array_pop($this->stack);
            $operation = new Operation($this->operands[$i], $op1, $op2, $this->workspace);
            \array_push($this->stack, $operation);
        }
        $this->processed = true;
    }

}

