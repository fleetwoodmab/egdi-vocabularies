<?php
include_once("app_params.php");
header("Access-Control-Allow-Origin: *");

function exception_handler($exception) {
    echo $exception->getMessage();
}

set_exception_handler('exception_handler');
$conn = sqlConnect();
$mysqli = mysqliConnect();

function sqlConnect() {
    global $mysql_server, $mysql_user, $mysql_pwd, $mysql_db;
    $conn = mysqli_connect($mysql_server, $mysql_user, $mysql_pwd);
    mysqli_select_db($conn, $mysql_db);
    mysqli_query($conn, "SET names utf8");
    mysqli_query($conn, "set character set utf8");

        return $conn;
}

function mysqliConnect() {
    global $mysql_server, $mysql_user, $mysql_pwd, $mysql_db;
    $mysqli = new mysqli($mysql_server, $mysql_user, $mysql_pwd, $mysql_db);

    if ($mysqli->connect_errno) {
        return null;
    }

    $mysqli->query("SET names utf8");
    $mysqli->query("set character set utf8");

    return $mysqli;
}


function mysqliPrepareStatement($query) {
    global $mysqli;
    return $mysqli->prepare($query);
}

function toSQLString(&$string) {
    if ($string==null || $string=="")
        $string = "NULL";
    else
        $string = "'" . str_replace("'", "''", $string) . "'";
}

function checkNotNull($string, $text, &$err) {
    if ($string==null || $string=="") {
        $err = "Value of " . $text . " must not be empty.";
        return false;
    }
    return true;
}

function toSQLInt(&$int) {
    if ($int==null || $int=="")
        $int = "NULL";
    else
        $int = (int) $int;
}


function parseSQLString(&$string) { 
    $string = str_replace("'", "''", $string);
}
function parseSQLInt(&$int) 
{ 
    $int = (int) $int;
}

function parseSQLDouble(&$double) {
$double = (double) $double;
}

function sqlExecute($query) {
    global $conn;
$r = mysqli_query($conn, $query);
sqlCheck($query);
return $r;
}

function sqlLineId($table, $condition) {
$sql = "select * from " . $table . " where " . $condition;
if (mysql_num_rows(sqlExecute($sql)) < 1)
    return -1;
else {
    list($id) = mysql_fetch_row(sqlExecute($sql));
    return $id;
}
}


function sqlCheck($command) {
    global $conn;
if (mysqli_errno($conn) != 0) {
    echo "Chyba: " . mysql_error() . "\nDetails: " . $command . "\n";
    throw new Exception( );
}
}

function sqlCell($command) {
    global $conn;
$r = mysqli_query($conn, $command);
sqlCheck($command);
$row = mysqli_fetch_array($r, MYSQLI_NUM);
mysqli_free_result($r);
return $row ? $row[0] : null;
}

function sqlRow($command, $mode = MYSQLI_BOTH) {
    global $conn;
$r = mysqli_query($conn, $command);
sqlCheck($command);
$row = mysqli_fetch_array($r, $mode);
mysqli_free_result($r);
return $row;
}

function sqlRows($query,$mode = MYSQLI_BOTH) {
$q = sqlExecute($query);
$a = Array();
while (($r = mysqli_fetch_array($q, $mode))) {
    $a[count($a)] = $r;
}
return $a;
}


function sqlArray($query,$mode = MYSQLI_NUM) {
    $q = sqlExecute($query);
    $a = Array();
    while (($r = mysqli_fetch_array($q, $mode))) {
        $row = Array();
        for ($i = 0; $i < count($r); $i++) {
            $row[count($row)] = $r[$i];
        }
        $a[count($a)] = $row;
    }
    return $a;
}

function sqlCol($query, $index) {
    $a = sqlArray($query);
    return $a[$index];
}

function sqlFormInsert($table, $cols) {
    $c = explode(",", $cols);
    $q = "insert into " . $table . "(";

    for ($i = 0; $i < count($c); $i++) {

        if ($i != 0)
            $q.=",";

        if (strpos($c[$i], "s:") !== false) {

            $q.=substr($c[$i], strpos($c[$i], ":") + 1);
        }

        else
        $q.=$c[$i];
    }

    $q.=") values(";

    for ($i = 0; $i < count($c); $i++) {

        if ($i != 0)
            $q.=",";

        $aux = explode(":", $cols[$i]);

        if (strpos($c[$i], "s:") !== false) {

            $q.="'";

            $q.=$_REQUEST[substr($c[$i], strpos($c[$i], ":") + 1)];

            $q.="'";
        }

        else
            $q.=$_REQUEST[$c[$i]];
    }

    $q.=")";

    sqlExecute($q);

    echo $q;

    return mysql_insert_id();
}


function getId() {
    $value = (isset($_REQUEST["id"])) ? ((int) $_REQUEST["id"]) : 0;
    return $value;
}

function formInit($fieldList) {

    $seznamPoli = explode(",", $fieldList);

    $cnt = count($seznamPoli);

    $result = Array();

    for ($i = 0; $i < $cnt; $i++)
        $result[$seznamPoli[$i]] = "";

    return $result;
}

function sqlDateFormat($d, $type=null) {
    if ($d == null)
        return "";

    $val = $d;
    #2008-10-02 00:00:00  -> 10.2.2008
    $datum = explode('-', $val);
    $datum2 = explode(' ', $datum[2]);
    $datum[2] = $datum2[0];
    $datum[3] = count($datum2) > 1 ? " " . $datum2[1] : "";

    $rok = $datum[0];
    $mes = $datum[1];
    $den = $datum[2];
    $cas = $datum[3];

    if ($type == 'D')
        $val = $den . '.' . $mes . '.' . $rok . $cas;
    else if ($type == 'M') {
        $cas2 = explode(":", $cas);
        $val = $den . '.' . $mes . '.' . $rok . $cas2[0] . ":" . $cas2[1];
    }
    else
        $val = $den . '.' . $mes . '.' . $rok;

    return $val;
}

function value2sql($s) {
    return ($s==null || $s=="")?"NULL":$s;
}

function cors_headers() {
    
    // Allow from any origin
    {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 3600');    // cache for 1 hour
    }
    
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
}

?>