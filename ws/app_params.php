<?php
include_once("/var/www/ticket.geoinformation.dev/files/editor_privates.php");
include_once("lib_sql.php");
header('Content-Type: application/json; charset=utf-8');
cors_headers();

header("Access-Control-Allow-Origin: *");
session_name("pvedit");
session_start();

$appTitle="Topic Manager";

function param($name) {
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
}
function sparam($name) {
    $r= isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
    if ($r==null) $r=$_SESSION[$name];
    else $_SESSION[$name]=$r;
    return $r;
}
function sql_param($name) {
    $a= isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
    if ($a!=null) {
        $a=str_replace("'", "''", $a);
    }
    return $a;
}
function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
?>