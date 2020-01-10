#!/usr/bin/php
<?php

// dead simple RPN implementation

$stack = [];

$stdin = fopen('php://stdin', 'r');

print '> ';
while ($ln = fgets($stdin)) {
    $ln = trim($ln);
    $res = false;
    if (!$ln || $ln == 'q') break;

    if (is_numeric($ln))
        array_push($stack, $ln);
    else {
        $v2 = array_pop($stack);
        $v1 = array_pop($stack);
        switch ($ln) {
            case ('+'): $res = $v1 + $v2; break;
            case ('-'): $res = $v1 - $v2; break;
            case ('*'): $res = $v1 * $v2; break;
            case ('/'): $res = $v1 / $v2; break;
            default: 
                print "Error: unknown operator\n";
                break 2;
        }
    }

    if (count($stack)) print "\033[30;01m" . implode(' ', $stack) . " \033[00;37m";
    if ($res !== false) { 
        print "$res\033[00m\n";
        array_push($stack, $res);
    } else print "\n";

    print '> ';
}
