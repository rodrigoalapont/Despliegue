<?php
 
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
 
$conn = new mysqli($host,$user,$pass,$db);
 
if ($conn->connect_error){
    // Este mensaje solo se mostrará si la conexión falla (host/user/pass/db incorrectos)
    die ("Error de conexión: ".$conn->connect_error);
}
 
?>