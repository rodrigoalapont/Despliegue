<?php
require 'conexion.php';

// Recoger filtros
$vinilo = $_GET['vinilo'] ?? '';
$ciudad = $_GET['ciudad'] ?? '';

// Consulta base
$sql = "SELECT o.*, v.nombre AS nombre_vinilo
        FROM opiniones o
        JOIN vinilos v ON o.vinilo_id = v.id
        WHERE 1";

$params = [];

// Filtro por vinilo
if (!empty($vinilo)) {
    $sql .= " AND v.id = ?";
    $params[] = $vinilo;
}

// Filtro por ciudad
if (!empty($ciudad)) {
    $sql .= " AND o.ciudad LIKE ?";
    $params[] = "%$ciudad%";
}

// Ejecutar consulta
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$opiniones = $stmt->fetchAll();
?>
<h2>Gestión de opiniones</h2>

<form method="GET">
    <input type="text" name="ciudad" placeholder="Filtrar por ciudad">

    <select name="vinilo">
        <option value="">Todos los vinilos</option>
        <?php
        $vinilos = $conexion->query("SELECT id, nombre FROM vinilos");
        foreach ($vinilos as $v) {
            echo "<option value='{$v['id']}'>{$v['nombre']}</option>";
        }
        ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Ciudad</th>
        <th>Comentario</th>
        <th>Vinilo</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($opiniones as $op): ?>
    <tr>
        <td><?= htmlspecialchars($op['nombre']) ?></td>
        <td><?= htmlspecialchars($op['ciudad']) ?></td>
        <td><?= htmlspecialchars($op['comentario']) ?></td>
        <td><?= htmlspecialchars($op['nombre_vinilo']) ?></td>
        <td>
            <a href="eliminar_opinion.php?id=<?= $op['id'] ?>"
               onclick="return confirm('¿Eliminar esta opinión?')">
               
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
