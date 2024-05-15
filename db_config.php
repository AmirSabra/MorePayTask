<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'sql111.infinityfree.com');
define('DB_USERNAME', 'if0_36552642');
define('DB_PASSWORD', '3DbjHRnlfg');
define('DB_NAME', 'if0_36552642_test');
 
/* Attempt to connect to MySQL database */
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
?>