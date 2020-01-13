<?php
namespace Math\RPN;

class Exception extends \Exception { }

class MalformedExpression extends Exception { 
    function __construct($msg) { 
        parent::__construct('Malformed expression or unknown operator: ' . $msg); 
    }
}

class InvalidUnaryOperation extends Exception { 
    function __construct($msg) { 
        parent::__construct('Incorrect Unary Operation: ' . $msg);
    }
}

class UnknownOperator extends Exception {
    function __construct($operator) {
        parent::__construct('Unknown Operator "' . $operator . '"');
    }
}

class Unsolvable extends Exception {
    function __construct($missing = []) {
        $msg = "Could not solve equation: \n";
        $msg .= \implode("\n", $missing);
        parent::__construct($msg);
    }
}

