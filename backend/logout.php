<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir fuera del panel
header("Location: catalogo.php");
exit;