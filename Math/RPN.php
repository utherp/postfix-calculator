<?php
require_once('RPN/Workspace.class.php');
require_once('RPN/Operation.class.php');
require_once('RPN/Exceptions.class.php');


// simple, single-use calls

// solve (or reduce) an expression
function rpn_solve($expr, $registers = []) {
    $ws = new \Math\RPN\Workspace($registers);
    $ws->add_expression($expr);
    return $ws->solve(0);
}


