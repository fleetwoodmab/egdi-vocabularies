<?php
include_once("/var/www/ticket.geoinformation.dev/files/editor_privates.php");
include_once("lib_sql.php");
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
cors_headers();

session_name("pvedit");
session_start();
$user = isset($_POST["user"])?$_POST["user"]:null;
$pwd = isset($_POST["password"])?$_POST["password"]:null;
if (isset($users[$user]) && $users[$user]["password"] === $pwd) {
    $_SESSION["user"] = $user;
        $result=Array(
    "user" => $user,
    "status" => "ok"
        );      
} else {
        http_response_code(400);
        $result=Array(
    "status" => "Invalid login, user [".$user."]."
        );
}

echo json_encode($result);
?>
