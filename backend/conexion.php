<?php
// conexion.php
 
$host = "localhost";
$user = "root"; // Usuario por defecto en XAMPP/WAMP
$pass = "";     // Contraseña vacía por defecto
$db = "login_midnight_wax";
 
$conn = new mysqli($host,$user,$pass,$db);
 
if ($conn->connect_error){
    // Este mensaje solo se mostrará si la conexión falla (host/user/pass/db incorrectos)
    die ("Error de conexión: ".$conn->connect_error);
}
 
?>