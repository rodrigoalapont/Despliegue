<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("conexion.php");

// Mensajes de error
$error_message = '';

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT usuario, password FROM usuarios WHERE usuario = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($db_usuario, $db_password);
            $stmt->fetch();

            // Comparación en texto plano (correcto para tu nivel actual)
            if ($password === $db_password) {
                $_SESSION['loggedin'] = true;
                $_SESSION['usuario']  = $db_usuario;

                header('Location: gestor_catalogo.php');
                exit;
            } else {
                $error_message = 'Usuario o contraseña incorrectos.';
            }
        } else {
            $error_message = 'Usuario o contraseña incorrectos.';
        }

        $stmt->close();
    } else {
        $error_message = 'Error en la consulta SQL.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-3">Gestión de Catálogo</h3>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="catalogo.php">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Iniciar sesión
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
</body>
</html>