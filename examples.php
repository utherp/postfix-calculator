<?php
require_once('Math/RPN.php');

// Create a new workspace
$workspace = new \Math\RPN\Workspace();

/* 
    supported mathematical symbols are:

    sym    operation        example     answer
    ---------------------------------------------------
    +      addition         2 5 +       7
    -      subtraction      5 3 -       2
    %      modulo           13 5 %      3
    * ⋅ ×  multiplication   5 5 *       25
    / ÷    division         40 5 /      8
    ^      exponent         3 3 ^       27
    √      root             2 100 √     10
    =      assignment       n 20 =      (assigns register N as 20)
*/


// add a few expressions
$workspace->add_expression('-1 7 10 33 83 * + - * 2 ^');
$workspace->add_expression('3 27 √');

// display the expressions and registers
dump_state($workspace);

// add an expression using a register called 'myVar'
$workspace->add_expression('3 4 * 5 myVar 20 + - -');

// note that the expression is reduced, but not solved, to: 12 5 myVar 20 + - -
dump_state($workspace);

// now lets add an expression that sets the register
$workspace->add_expression('myVar 5 =');

// now we can see the expression has been solved to: 32
dump_state($workspace);


