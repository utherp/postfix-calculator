<?php
require_once('Math/RPN.php');
@session_name('rpn');
@session_start();

use \Math\RPN as RPN;

$workspace = &$_SESSION['workspace'];
if (!$workspace) {
    $workspace = new RPN\Workspace();
    $_SESSION['workspace'] = &$workspace;
}


