GNU nano 6.2                                                                         loginCors.php                                                                                   
<?php
include_once("/var/www/ticket.geoinformation.dev/files/editor_privates.php");
include_once("lib_sql.php");
header('Content-Type: application/json; charset=utf-8');
cors_headers();

$user = isset($_POST["user"]) ? $_POST["user"] : null;
$pwd = isset($_POST["password"]) ? $_POST["password"] : null;

$authenticated = false;
if (isset($users[$user]) && $users[$user]["password"] === $pwd) {
    $authenticated = true;
    $_SESSION["user"] = $user;
}

if ($authenticated) {
    $result = array(
        "user" => $user,
        "status" => "ok"
    );
} else {
    http_response_code(400);
    $result = array(
        "status" => "Invalid login, user [" . $user . "]."
    );
}

echo json_encode($result);
?>

