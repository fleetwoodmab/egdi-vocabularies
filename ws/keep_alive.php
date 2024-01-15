GNU nano 6.2                                                                         keep_alive.php                                                                                  
<?php
session_name("pvedit");
session_start();
include_once("lib_sql.php");
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
cors_headers();


if (isset($_SESSION["user"])) {
        $user = $_SESSION["user"];
        $result=Array("status" => "ok", "user" => $user);
} else {
        $result=Array("status" => "Not logged in.");
}
echo json_encode($result);

?>
