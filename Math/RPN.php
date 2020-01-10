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


// some simple console display funciions
function dump_state ($ws) {
    output_expr($ws);
    output_reg($ws);
}

function output_expr ($ws) {
    print "\nExpressions\n================================================================\n";
    print " idx | reduced               |  expression\n";
    foreach ($ws->expressions as $i => $expr)
        printf("----------------------------------------------------------------\n %3u | %20s  |  %s\n", $i, $expr . '', $expr->express());
    print "================================================================\n";
}

function output_reg ($ws) {
    if (!count($ws->registers)) {
        print "No registers\n";
        return;
    }
    print "\nRegisters\n======================================================\n";
    print " name      | value\n";
    foreach ($ws->registers as $k => $reg) {
        $v = is_object($reg) ? $reg->reduce() : $reg;
        printf("------------------------------------------------------\n%10s | %s\n", $k, $v);
    }
    print "======================================================\n";
}


