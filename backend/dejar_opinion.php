<?php
session_start();
require_once 'conexion.php';

// 1. Obtener el ID del vinilo
$vinilo_id = $_GET['id'] ?? 0;
$vinilo_nombre = '';

if ($vinilo_id > 0) {
    $sql = "SELECT nombre_vinilo FROM vinilos WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $vinilo_id);
        $stmt->execute();
        $stmt->bind_result($vinilo_nombre);
        $stmt->fetch();
        $stmt->close();
    }
}

// Si no se encuentra el vinilo, redirigir
if (empty($vinilo_nombre)) {
    header('Location: listado_vinilos.php');
    exit;
}

// 2. Procesar el formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    
    if (!empty($nombre) && !empty($ciudad) && !empty($comentario)) {
        $sql = "INSERT INTO opiniones (vinilo_id, nombre_usuario, ciudad, comentario) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isss", $vinilo_id, $nombre, $ciudad, $comentario);
            if ($stmt->execute()) {
                $mensaje = '<div class="alert alert-success">¡Gracias por tu opinión! Redirigiendo...</div>';
                echo '<meta http-equiv="refresh" content="2;url=listado_vinilos.php">';
            } else {
                $mensaje = '<div class="alert alert-danger">Error al guardar la opinión.</div>';
            }
            $stmt->close();
        }
    } else {
        $mensaje = '<div class="alert alert-warning">Por favor, rellena todos los campos.</div>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dejar Opinión - <?php echo htmlspecialchars($vinilo_nombre); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/estilos.css">
    <style>
        body { background-color: #0a0b1a !important; color: #e1dddd; }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #1a1b33;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            border: 1px solid #4b4e77;
        }
        .form-control {
            background-color: #0a0b1a;
            color: #e1dddd;
            border-color: #4b4e77;
        }
        .form-control:focus {
            background-color: #0d0e22;
            color: #fff;
            border-color: #5a5ad0;
            box-shadow: 0 0 0 0.25rem rgba(90, 90, 208, 0.25);
        }
        .btn-custom {
            background-color: #5a5ad0;
            border-color: #5a5ad0;
            color: white;
            padding: 10px 30px;
            font-weight: 600;
        }
        .btn-custom:hover {
            background-color: #7a7ae6;
            border-color: #7a7ae6;
            color: white;
        }
        .vinyl-highlight { color: #5a5ad0; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="mb-4 text-center">Deja tu opinión sobre <br><span class="vinyl-highlight"><?php echo htmlspecialchars($vinilo_nombre); ?></span></h2>
        
        <?php echo $mensaje; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Tu Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Escribe tu nombre">
            </div>
            <div class="mb-3">
                <label for="ciudad" class="form-label">Ciudad</label>
                <input type="text" class="form-control" id="ciudad" name="ciudad" required placeholder="Escribe tu ciudad">
            </div>
            <div class="mb-3">
                <label for="comentario" class="form-label">Tu Comentario</label>
                <textarea class="form-control" id="comentario" name="comentario" rows="5" required placeholder="¿Qué te ha parecido este vinilo?"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="listado_vinilos.php" class="text-white text-decoration-none">&larr; Volver al catálogo</a>
                <button type="submit" class="btn btn-custom">Enviar Opinión</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
