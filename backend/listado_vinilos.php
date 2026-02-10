<?php
session_start();

// 2. Incluir conexión
require_once 'conexion.php';

// 3. Obtener solo los vinilos visibles para el público
$sql = "SELECT * FROM vinilos WHERE visible = 1 ORDER BY id DESC";
$result = $conn->query($sql);

$vinilos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vinilos[] = $row;
    }
}

// 4. Obtener todas las opiniones con el nombre del vinilo
$sql_opiniones = "SELECT o.*, v.nombre_vinilo 
                  FROM opiniones o 
                  JOIN vinilos v ON o.vinilo_id = v.id 
                  ORDER BY o.fecha DESC";
$result_opiniones = $conn->query($sql_opiniones);

$opiniones = [];
if ($result_opiniones) {
    while ($row = $result_opiniones->fetch_assoc()) {
        $opiniones[] = $row;
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
        /* Estilos Carousel */
        .carousel-item { padding: 20px; }
        .review-card {
            background-color: #1a1b33;
            border: 1px solid #4b4e77;
            border-radius: 12px;
            padding: 25px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .review-text { font-style: italic; font-size: 1.1rem; border-left: 3px solid #5a5ad0; padding-left: 15px; margin-bottom: 20px; }
        .review-author { font-weight: bold; color: #5a5ad0; }
        .review-meta { font-size: 0.85rem; color: #a1a1a1; }
        .carousel-control-prev, .carousel-control-next { width: 5%; }
    </style>
</head>
<body>

<div class="container listing-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white">Catálogo de Vinilos</h1>
            <p class="text-white opacity-75">Explora nuestra colección y deja tu opinión</p>
        </div>
        <div class="d-flex gap-2">
            <a href="https://despliegue-gilt.vercel.app/" class="btn btn-custom">Volver a Home</a>
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
                            <div class="price-tag mb-3"><?php echo number_format($v['precio'], 2, ',', '.'); ?>€</div>
                            <a href="dejar_opinion.php?id=<?php echo $v['id']; ?>" class="btn btn-custom btn-sm w-100">Dejar Opinión</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- SECCIÓN DE OPINIONES (CARRUSEL) -->
    <div class="mt-5 pt-5 pb-5">
        <h2 class="text-white text-center mb-4">Lo que dicen nuestros clientes</h2>
        <hr class="mb-5 mx-auto" style="border-color: #4b4e77; width: 100px; border-width: 3px;">

        <?php if (empty($opiniones)): ?>
            <div class="text-center text-muted">Aún no hay opiniones. ¡Sé el primero en dejar una!</div>
        <?php else: ?>
            <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($opiniones as $index => $op): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="review-card">
                                <div class="review-text">
                                    "<?php echo htmlspecialchars($op['comentario']); ?>"
                                </div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div class="review-author"><?php echo htmlspecialchars($op['nombre_usuario']); ?></div>
                                        <div class="review-meta"><?php echo htmlspecialchars($op['ciudad']); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-dark border border-secondary"><?php echo htmlspecialchars($op['nombre_vinilo']); ?></div>
                                        <div class="review-meta mt-1"><?php echo date('d/m/Y', strtotime($op['fecha'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($opiniones) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
