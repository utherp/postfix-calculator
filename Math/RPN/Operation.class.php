<?php
namespace Math\RPN;
require_once('Exceptions.class.php');

class Operation {
    public $operands = [ NULL, NULL ];
    public $operator = false;
    public $solution = false;
    public $solvable = false;
    public $missing;
    public $workspace = false;

    private $reduced = false;

    function __construct ($operator, $operand1, $operand2, &$workspace = false) {
        $this->operator = $operator;
        $this->operands[0] = $operand1;
        $this->operands[1] = $operand2;
        $this->workspace = &$workspace;
    }

    public function express () {
        $expr = [];
        foreach ($this->operands as $oper)
            $expr[] = ($oper instanceof self) ? $oper->express() : $oper;
        return \implode(' ', $expr) . ' ' . $this->operator;
    }
    /***************************************************
     * reduce:  reduces the operation to its minimally
     *          expressive representation.  If it can be
     *          solved, it returns the solution, otherwise
     *          it returns an array representing the 
     *          reduced equation
     */
    public function reduce($solve = true) {
        $vals = [];
        $this->missing = [];
        $this->solvable = true;

        foreach ($this->operands as $i => $v) {
            if (!isset($v)) {
                // an operand was not provided
                $vals[] = '[missing operand]';
                $this->solvable = false;
                $this->missing[] = 'operand' . ($i+1);
                continue;
            }

            // our operand is a variable reference to a register
            if (\is_string($v) && !\is_numeric($v)) {
                if (($i === 0) && ($this->operator === '=')) {
                    // ...a special case for register assignment
                    $this->workspace->registers[$v] = &$this;
                    $vals[] = $v;
                    continue;
                }
                if (!key_exists($v, $this->workspace->registers)) {
                    $this->solvable = false;
                    $vals[] = $v;
                    $this->missing[] = 'register ' . $v;
                    continue;
                } else
                    $v = &$this->workspace->registers[$v];
            }

            if ($v instanceof self) {
                // an operand is another operation, reduce it
                $tmp = $v->reduce();

                if (!\is_numeric($tmp)) {
                    $this->solvable = false;
                    $this->missing = array_merge($this->missing, $v->missing);
                    $vals = array_merge($vals, $tmp);
                }
                $v = $tmp;
            }

            if (\is_numeric($v)) {
                $vals[] = $v;
                continue;
            }
        }

        if ($this->solvable) {
            if (!$solve) return $vals;
            $solution = $this->solve($vals);
            return $solution;
        }
        $vals[] = $this->operator;
        return $vals;
    }

    public function solve ($vals = false) {
        if (\is_array($vals)) {
            $vals = $this->reduce(false);
        }

        // if the operation is unsolvable, throw an exception
        if (!$this->solvable) {
            throw new Unsolvable($this->missing);
        }

        // otherwize, return the solution.
        return $this->solution = $this->_eval($vals);
    }

    private function _eval($vals) {
        // evaluate the solution.
        // NOTE: solvable operations will always have exactly 2 operands
        // *except* assignment

        switch ($this->operator) {
            // assignment
            case ('='): return $vals[1];
            // add
            case ('+'): return $vals[0] + $vals[1];
            // subtract
            case ('-'): return $vals[0] - $vals[1];
            // divide
            case ('÷'):
            case ('/'): return $vals[0] / $vals[1];
            // multiply
            case ('⋅'):
            case ('×'):
            case ('*'): return $vals[0] * $vals[1];
            // modulo
            case ('%'): return $vals[0] % $vals[1];
            // exponent
            case ('^'): return \pow($vals[0], $vals[1]);
            // root 
            case ('√'): return \pow($vals[1], 1 / $vals[0]);
            // (NOTE: there are NO unary operators in RPN! 
            // for a square root, the first operand *must* be 2)
            default:
                throw new UnknownOperator($this->operator);
        }
    }

    function __toString () {
        $reduced = $this->reduce();
        return \is_array($reduced) ? \implode(' ', $reduced) : $reduced . '';
    }

}

