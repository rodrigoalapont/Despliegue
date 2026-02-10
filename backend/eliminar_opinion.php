<?php
require 'conexion.php';

$id = $_GET['id'];

$stmt = $conexion->prepare("DELETE FROM opiniones WHERE id = ?");
$stmt->execute([$id]);

header("Location: gestor_opiniones.php");
exit;
