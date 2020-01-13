#!/usr/bin/php
<?php
require_once('lib/Math/RPN.php');

array_shift($argv);

// argument mode
if (count($argv) && $argv[0] == '--') {
    array_shift($argv);
    $expr = implode(' ', $argv);
    print rpn_solve($expr) . "\n";
    exit;
}

// cli mode

function help () {
    print <<<EOF
q: quit
r: display variable registry
e: show expressions
operators:
    +: add
    -: subtract
    *: multiply
    /: divide
    =: assign to a register


EOF;
}

function process_input($ln) {
    // generally, I hate globals.. but there is no harm
    // here as they are very application specific
    global $workspace, $expr;

    if ($ln === "\n") $parts = [];
    else $parts = preg_split('/\s+/', trim($ln));
    $part = NULL;
    if (!count($parts)) array_push($expr, $part = ';');
    else while (count($parts)) {
        $part = @array_shift($parts);
        $expr[] = $part;
    }

    if (substr($part, -1) === ';') {
        array_pop($expr);
        $expr[] = substr($part, 0, -1);
        $expr[] = $part = ';';
    }

    if ($part === ';') {
        array_pop($expr);
        try {
            $expression = $workspace->add_expression($expr);
            $solution = $expression . ''; //rpn_solve(implode(' ', $expr));
            print $solution . "\n";
            $expr = [];
        } catch (\Math\RPN\InvalidUnaryOperation $e) {
            print $e->getMessage() . "\n";
            $expr = [];
        }
    }

    return;
}

function show_expressions () {
    global $workspace;
    print "\nExpressions\n================================================================\n";
    print " idx | reduced               |  expression\n";
    foreach ($workspace->expressions as $i => $expr)
        printf("----------------------------------------------------------------\n %3u | %20s  |  %s\n", $i, $expr . '', $expr->express());
    print "================================================================\n";
}

function show_registry() {
    global $registry;
    print "\nRegisters\n======================================================\n";
    print " name      | value\n";
    foreach ($registry as $k => $reg) {
        if (is_object($reg)) $reg = $reg . '';
        printf("------------------------------------------------------\n%10s | %s\n", $k, $reg);
    }
    print "======================================================\n";
}


print "Enter an RPN expression, finish with a semicolon or a blank line\nType '?' for help\n\n";
$registry = [];
$workspace = new \Math\RPN\Workspace($registry);

$stdin = fopen('php://stdin', 'r');
$expr = [];
while (true) {
    while (true) {
        print '> ';
        $ln = fgets($stdin);
        if ($ln === false) $ln = 'q';
        switch (strtolower(trim($ln))) {
            case ('q'): break 3;
            case ('h'):
            case ('?'): help(); continue;
            case ('r'): show_registry(); continue;
            case ('e'): show_expressions(); continue;
            default: process_input($ln);
        }
    }
}

@fclose($stdin);
