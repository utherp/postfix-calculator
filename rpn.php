#!/usr/bin/php
<?php
require_once('Math/RPN.php');

array_shift($argv);

// argument mode
if (count($argv) && $argv[0] == '--') {
    array_shift($argv);
    $expr = implode(' ', $argv);
    print rpn_solve($expr) . "\n";
    exit;
}

// cli mode

print "Enter an RPN expression.\nFinish with q, a blank newline or EOF\n";
print "type q to quit\n";

$stdin = fopen('php://stdin', 'r');

$expr = '';
while (true) {
    print '> ';
    $ln = fgets($stdin);
    if ($ln === false) break;
    $ln = trim($ln);
    if (!$ln) break;
    $expr .= ' ' . trim($ln);
    if (preg_match('/\sq$/', $expr)) {
        $expr = substr($expr, 0, -2);
        break;
    }
}

@fclose($stdin);

print rpn_solve(trim($expr)) . "\n";
