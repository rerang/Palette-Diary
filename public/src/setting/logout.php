<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

$exp = 0;
header('Content-type: application/json'); 
// setcookie('authorization', "none", $exp);
unset($_COOKIE["authorization"]);setcookie('authorization', '', time() - 1);

?>
