<?php
session_start();

// 1. Verificar la sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // 🚨 CORRECCIÓN: Redirige al archivo de login correcto
    header('Location: catalogo.php'); 
    exit;
}

// 2. Incluir la configuración de la base de datos
// Nota: Aquí se usa 'conexion.php' ya que no tienes 'db_config.php'
require_once 'conexion.php'; 

// Mensajes de estado
$status_message = '';

// --- LÓGICA DE PROCESAMIENTO ---

// A. Lógica para AÑADIR VINILO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'anadir') {
    $foto_url = $_POST['foto_url'] ?? '';
    $nombre_vinilo = $_POST['nombre_vinilo'] ?? '';
    $nombre_artista = $_POST['nombre_artista'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0.00;
    $anio = $_POST['anio'] ?? 0;
    
    // Consulta preparada para mayor seguridad (La tabla debe ser 'vinilos')
    $sql = "INSERT INTO vinilos (foto_url, nombre_vinilo, nombre_artista, descripcion, precio, anio, visible) VALUES (?, ?, ?, ?, ?, ?, 1)";
    
    if ($stmt = $conn->prepare($sql)) {
        // Enlazar parámetros
        $stmt->bind_param("ssssdi", $foto_url, $nombre_vinilo, $nombre_artista, $descripcion, $precio, $anio);
        
        if ($stmt->execute()) {
            $status_message = '<div class="alert alert-success">Vinilo "' . htmlspecialchars($nombre_vinilo) . '" añadido con éxito.</div>';
        } else {
            $status_message = '<div class="alert alert-danger">ERROR: No se pudo ejecutar la inserción. ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $status_message = '<div class="alert alert-danger">Error al preparar la consulta de inserción.</div>';
    }
}

// B. Lógica para BUSCAR y MOSTRAR/OCULTAR/BORRAR
$vinilos_resultado = [];
$search_term = '';

if (isset($_GET['search_term'])) {
    $search_term = trim($_GET['search_term']);
}

// Si hay término de búsqueda O si se está actualizando un vinilo (para recargar la lista)
if (!empty($search_term) || isset($_POST['accion'])) {
    $search_pattern = "%" . $search_term . "%";
    $sql = "SELECT id, nombre_vinilo, nombre_artista, visible FROM vinilos WHERE nombre_vinilo LIKE ? OR nombre_artista LIKE ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $search_pattern, $search_pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $vinilos_resultado[] = $row;
        }
        $stmt->close();
    }
}

// C. Lógica para MOSTRAR/OCULTAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'toggle_visible') {
    $id = $_POST['id'] ?? 0;
    $new_visible = $_POST['new_visible'] ?? 0;
    
    $sql = "UPDATE vinilos SET visible = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $new_visible, $id);
        if (!$stmt->execute()) {
            $status_message = '<div class="alert alert-danger">ERROR al actualizar la visibilidad.</div>';
        }
        $stmt->close();
    }
    // Re-ejecutar la búsqueda para actualizar la tabla
    header("Location: gestor_catalogo.php?search_term=" . urlencode($search_term));
    exit;
}

// D. Lógica para BORRAR VINILO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {
    $id = $_POST['id'] ?? 0;
    
    $sql = "DELETE FROM vinilos WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $status_message = '<div class="alert alert-success">Vinilo con ID ' . $id . ' borrado con éxito.</div>';
        } else {
            $status_message = '<div class="alert alert-danger">ERROR al borrar el vinilo.</div>';
        }
        $stmt->close();
    }
    // Re-ejecutar la búsqueda para actualizar la tabla
    header("Location: gestor_catalogo.php?search_term=" . urlencode($search_term));
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Catálogo - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos específicos del panel */
        body { background-color: #0a0b1a !important; color: #e1dddd; }
        .admin-container { background-color: #1a1b33; padding: 30px; border-radius: 10px; margin-top: 50px; }
        .module-card { background-color: #2c3e50; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #4b4e77;}
        .module-card h3 { color: #5a5ad0; margin-bottom: 20px; border-bottom: 2px solid #5a5ad0; padding-bottom: 10px;}
        .btn-custom-add { background-color: #5a5ad0; border-color: #5a5ad0; color: white; }
        .btn-custom-add:hover { background-color: #7a7ae6; border-color: #7a7ae6; }
        .table { color: #e1dddd; }
        .table th { color: #5a5ad0; }
        .form-control { background-color: #0a0b1a; color: #e1dddd; border-color: #4b4e77; }
    </style>
</head>
<body>

<div class="container admin-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Panel de Gestión de Catálogo</h1>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>

    <hr>
    <?php echo $status_message; ?>

    <div class="module-card">
        <h3>💿 Añadir Nuevo Vinilo</h3>
        <form action="gestor_catalogo.php" method="POST"> 
            <input type="hidden" name="accion" value="anadir">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre_vinilo" class="form-label">Nombre del Vinilo</label>
                    <input type="text" class="form-control" id="nombre_vinilo" name="nombre_vinilo" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nombre_artista" class="form-label">Nombre del Artista</label>
                    <input type="text" class="form-control" id="nombre_artista" name="nombre_artista" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="foto_url" class="form-label">URL de la Foto</label>
                <input type="text" class="form-control" id="foto_url" name="foto_url" placeholder="Ej: media/portadadisco.jpg" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Vinilo</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio (€)</label>
                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="anio" class="form-label">Año de Lanzamiento</label>
                    <input type="number" class="form-control" id="anio" name="anio" min="1900" max="<?php echo date('Y'); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-custom-add">Guardar Vinilo</button>
        </form>
    </div>

    <div class="module-card">
        <h3>🔍 Buscar y Gestionar Vinilos</h3>
        <form action="gestor_catalogo.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar por nombre de vinilo o artista..." name="search_term" value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="btn btn-outline-light" type="submit">Buscar</button>
            </div>
        </form>

        <?php if (!empty($vinilos_resultado)): ?>
            <p>Resultados de la búsqueda: (<?php echo count($vinilos_resultado); ?>)</p>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Vinilo</th>
                            <th>Artista</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vinilos_resultado as $vinilo): ?>
                            <tr>
                                <td><?php echo $vinilo['id']; ?></td>
                                <td><?php echo htmlspecialchars($vinilo['nombre_vinilo']); ?></td>
                                <td><?php echo htmlspecialchars($vinilo['nombre_artista']); ?></td>
                                <td>
                                    <span class="badge <?php echo $vinilo['visible'] ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $vinilo['visible'] ? 'Visible' : 'Oculto'; ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="gestor_catalogo.php?search_term=<?php echo urlencode($search_term); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="toggle_visible">
                                        <input type="hidden" name="id" value="<?php echo $vinilo['id']; ?>">
                                        <input type="hidden" name="new_visible" value="<?php echo $vinilo['visible'] ? 0 : 1; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $vinilo['visible'] ? 'btn-warning' : 'btn-success'; ?>">
                                            <?php echo $vinilo['visible'] ? 'Ocultar' : 'Mostrar'; ?>
                                        </button>
                                    </form>

                                    <form action="gestor_catalogo.php?search_term=<?php echo urlencode($search_term); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas BORRAR permanentemente este vinilo?');">
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="id" value="<?php echo $vinilo['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (isset($_GET['search_term']) && empty($vinilos_resultado)): ?>
             <div class="alert alert-info">No se encontraron vinilos que coincidan con "<?php echo htmlspecialchars($search_term); ?>".</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>