<?php

namespace Math\RPN {
    require_once('RPN/Workspace.class.php');
    require_once('RPN/Operation.class.php');
    require_once('RPN/Registry.class.php');
    require_once('RPN/Expression.class.php');
    require_once('RPN/Exceptions.class.php');


    //
    // Test for UTF-8 support
    //

    // iconv module is required for UTF-8 support
    if (function_exists('iconv_strlen')) {
        // UTF-8 support
        \define(__NAMESPACE__ . '\OPS', '+-%*/^=⋅×÷√');
        function strlen() { return call_user_func_array('\iconv_strlen', func_get_args()); }
        function strpos() { return call_user_func_array('\iconv_strpos', func_get_args()); }
    } else {
        // no UTF-8 support
        \define(__NAMESPACE__ . '\RPN\OPS', '+-%*/^=');
    }

    // is_operator(op): determine if a character is an operator (or operand)
    // note: these str functions do *not* reference the global namespace intentionally
    function is_operator($o = NULL) { return ((strlen($o) === 1) && (strpos(OPS, $o) !== false)); }

    // split a string into operators and operands
    // this is really just a quick utf8-friendly 
    // white-space splitter that doesn't require mbstring
    function parse ($str) { 
        $args = []; 
        $v = '';
        // note: this is meant to use the global version of strlen
        $len = \strlen($str);
        for ($i = 0; $i < $len; $i++) {
            switch ($str{$i}) {
                case (' '):
                case ("\n"):
                case ("\t"):
                    if ($v !== '') $args[] = $v;
                    $v = '';
                    continue;
                default:
                    $v .= $str{$i};
            }
        }
        if ($v !== '') $args[] = $v;
        return $args;
    }


    // a simple expression solver without implementing the objects
    function solve($expr, $registers = []) {
        $ws = new Workspace($registers);
        $ws->add_expression($expr);
        return $ws->solve(0);
    }
}

