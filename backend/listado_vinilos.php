<?php
session_start();

// 1. Verificar la sesión (opcional, pero recomendado si es parte del panel de admin)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: catalogo.php');
    exit;
}

// 2. Incluir conexión
require_once 'conexion.php';

// 3. Obtener todos los vinilos
$sql = "SELECT * FROM vinilos ORDER BY id DESC";
$result = $conn->query($sql);

$vinilos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vinilos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Vinilos - Midnight Wax</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos del proyecto -->
    <link rel="stylesheet" href="../frontend/estilos.css">
    <style>
        body {
            background-color: #0a0b1a !important; /* Asegurar fondo oscuro */
            color: #e1dddd;
        }
        .listing-container {
            background-color: #1a1b33;
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .vinyl-card {
            background-color: #2c3e50;
            border: 1px solid #4b4e77;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .vinyl-card:hover {
            transform: translateY(-5px);
            border-color: #5a5ad0;
        }
        .vinyl-img-container {
            height: 250px;
            overflow: hidden;
            background-color: #000;
        }
        .vinyl-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .vinyl-details {
            padding: 15px;
            flex-grow: 1;
        }
        .vinyl-title {
            color: #5a5ad0;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .vinyl-artist {
            font-size: 0.9rem;
            color: #a1a1a1;
            margin-bottom: 10px;
        }
        .vinyl-info {
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        .price-tag {
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff;
            margin-top: 10px;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .btn-custom {
            background-color: #5a5ad0;
            border-color: #5a5ad0;
            color: white;
        }
        .btn-custom:hover {
            background-color: #7a7ae6;
            border-color: #7a7ae6;
            color: white;
        }
    </style>
</head>
<body>

<div class="container listing-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white">Listado Completo de Vinilos</h1>
            <p class="text-white opacity-75">Vista previa de todo el catálogo en la base de datos</p>
        </div>
        <div class="d-flex gap-2">
            <a href="gestor_catalogo.php" class="btn btn-outline-light">Volver al Gestor</a>
            <a href="https://despliegue-gilt.vercel.app" class="btn btn-custom">Volver a Home</a>
        </div>
    </div>

    <hr class="mb-5" style="border-color: #4b4e77;">

    <div class="row g-4">
        <?php if (empty($vinilos)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">No hay vinilos en la base de datos.</div>
            </div>
        <?php else: ?>
            <?php foreach ($vinilos as $v): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="vinyl-card position-relative">
                        <span class="badge status-badge <?php echo $v['visible'] ? 'bg-success' : 'bg-warning'; ?>">
                            <?php echo $v['visible'] ? 'Visible' : 'Oculto'; ?>
                        </span>
                        <div class="vinyl-img-container">
                            <!-- Ruta corregida si las fotos están en frontend/media -->
                            <?php 
                            $img_path = $v['foto_url'];
                            // Si la ruta comienza con media/ pero el archivo está en ../frontend/media/
                            if (strpos($img_path, 'media/') === 0) {
                                $img_src = '../frontend/' . $img_path;
                            } else {
                                $img_src = $img_path;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($v['nombre_vinilo']); ?>" class="vinyl-img">
                        </div>
                        <div class="vinyl-details">
                            <div class="vinyl-title"><?php echo htmlspecialchars($v['nombre_vinilo']); ?></div>
                            <div class="vinyl-artist"><?php echo htmlspecialchars($v['nombre_artista']); ?></div>
                            <div class="vinyl-info"><strong>Año:</strong> <?php echo htmlspecialchars($v['year']); ?></div>
                            <div class="vinyl-info text-truncate"><?php echo htmlspecialchars($v['descripcion']); ?></div>
                            <div class="price-tag"><?php echo number_format($v['precio'], 2, ',', '.'); ?>€</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
