<?php
require_once 'conexion.php';

$vinilos = [];

$sql = "SELECT foto_url, nombre_vinilo, precio
        FROM vinilos
        WHERE visible = 1
        ORDER BY id DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vinilos[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($vinilos);