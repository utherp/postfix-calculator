# postfix-calculator
RPN, a postfix library and command line calculator


What is postfix?

Postfix or RPN (reverse polish notation) is a mathematical expression notation which requires no parentheses and, thus, no PEMDAS (order of operation).  You can read more about it here:  https://en.wikipedia.org/wiki/Reverse_Polish_notation



Why a library for something so basic?

I am firmly against writing the same thing twice.  A library of classes makes reuse easy.



How do I install it?

Checkout this repository out and require_once("lib/Math/RPN.php").  This will automatically include all other necessary files.



How does it work?

All interaction is performed via the Math\RPN\Workspace class.  Expressions are added and share the same Registry.  The registry is where the registers (variables) are stored.  After added, an expression can be retrieved in its original form, or reduced.  An expression is reduced by performing all operations with which all operands are available.  If an operation uses an undefined register, then it cannot be reduced. 

for example, this expression cannot be solved:

total 25 5 - /

cannot be solved, but it can be reduced to:

total 20 /

Adding the following expression into the workspace will assign the result to the register 'total':

total 25 4 * =

which reduces to:

total 100 =

...and now the first expression can be solved:

total 25 5 - / 
---------------
total 20 /
---------------
100 20 /
---------------
5

You can instantiate the workspace with your own array (or array-like object) containing your registry:

$registry = [ total => 100 ];
$workspace = new Math\RPN\Workspace($registry);

Note: The passed array or object is referenced, so changes made to the regsitry within the library will be reflected and vice versa



The Calculator:

The calculator itself is the script 'rpn_calc.php'.  '?' will display the following help:

Enter an RPN expression, finish with a semicolon or a blank line
Type '?' for help

> ?
q: quit
r: display variable registry
e: show expressions
operators:
    +: add
    -: subtract
    *: multiply
    /: divide
    =: assign to a register

> 

It takes expressions, terminated by a semicolon or an empty newline.


Example:
Consider the following:  Four kids working a drink stand sold 20 sodas for $2 a piece.  They purchased the sodas for 75 cents each.  The following expressions entered into the calculator will calculate what each kid made in profit (assuming no other overhead):


Enter an RPN expression, finish with a semicolon or a blank line
Type '?' for help

> total profit sold * =;
total profit sold * =
> price 2 =;
2
> cost .75 =;
.75
> profit price cost - =;
1.25
> e

Expressions
================================================================
 idx | reduced               |  expression
----------------------------------------------------------------
   0 | total profit sold * =  |  total profit sold * =
----------------------------------------------------------------
   1 |                    2  |  price 2 =
----------------------------------------------------------------
   2 |                  .75  |  cost .75 =
----------------------------------------------------------------
   3 |                 1.25  |  profit price cost - =
================================================================

But wait, there is still an unreduced expression... lets check the registry:

> r
Registers
======================================================
 name      | value
------------------------------------------------------
total      | total profit sold * =
------------------------------------------------------
     price | 2
------------------------------------------------------
      cost | .75
------------------------------------------------------
profit | 1.25
======================================================

Oh, for got to enter how many were sold...

> sold 20 =;
20
> e

Expressions
================================================================
 idx | reduced               |  expression
----------------------------------------------------------------
   0 |                   25  |  total profit sold * =
----------------------------------------------------------------
   1 |                    2  |  price 2 =
----------------------------------------------------------------
   2 |                  .75  |  cost .75 =
----------------------------------------------------------------
   3 |                 1.25  |  profit price cost - =
================================================================

Thanks better, now we'll divide up the profits by the workers

> workers 4 =;
4
> shares total workers / =;
6.25


Now lets see all the registers and expressions:

> r
Registers
======================================================
 name      | value
------------------------------------------------------
     total | 25
------------------------------------------------------
     price | 2
------------------------------------------------------
      cost | .75
------------------------------------------------------
      sold | 20
------------------------------------------------------
    profit | 1.25
------------------------------------------------------
   workers | 4
------------------------------------------------------
    shares | 6.25
======================================================

> e
Expressions
================================================================
 idx | reduced               |  expression
----------------------------------------------------------------
   0 |                   25  |  total profit sold * =
----------------------------------------------------------------
   1 |                    2  |  price 2 =
----------------------------------------------------------------
   2 |                  .75  |  cost .75 =
----------------------------------------------------------------
   3 |                   20  |  sold 20 =
----------------------------------------------------------------
   4 |                 1.25  |  profit price cost - =
----------------------------------------------------------------
   5 |                    4  |  workers 4 =
----------------------------------------------------------------
   6 |                 6.25  |  shares total workers / =
================================================================


