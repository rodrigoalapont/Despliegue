<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Iniciar la sesión al principio del archivo
session_start();
include("conexion.php");

// Variables para mensajes de error
$error_message = '';

// 1. Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    // Consulta para buscar el usuario
    $sql = "SELECT usuario, contraseña FROM login WHERE usuario = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Enlazar el parámetro 'usuario'
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Usuario encontrado, enlazar resultados
            $stmt->bind_result($db_usuario, $db_contraseña);
            $stmt->fetch();

            // Autenticación exitosa (comparación de texto plano)
            if ($contraseña === $db_contraseña) { 
                $_SESSION['loggedin'] = true;
                $_SESSION['usuario'] = $db_usuario;
                
                // 🚨 CORRECCIÓN CRÍTICA: Redirige al nombre de archivo correcto
                header('Location: gestor_catalogo.php'); 
                exit;
            } else {
                // Contraseña incorrecta
                $error_message = 'Usuario o contraseña incorrectos.';
            }
        } else {
            // Usuario no encontrado
            $error_message = 'Usuario o contraseña incorrectos.';
        }
        $stmt->close();
    } else {
        $error_message = 'Error de consulta SQL.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="login_custom.css"> 
    
    <title>Iniciar Sesión - Gestión de Catálogo</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 shadow-lg custom-login-card">
                    <h2 class="card-title text-center mb-4" style="color: #5a5ad0;">Gestión de Catálogo</h2>
                    <p class="text-center mb-4">Introduce tus credenciales para acceder a la administración.</p>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form action="catalogo.php" method="POST"> 
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="contraseña" class="form-label">Contraseña:</label>
                            <input type="password" id="contraseña" name="contraseña" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn w-100 mb-3 custom-login-btn">Iniciar Sesión</button>
                    </form>

                    <p class="text-center back-link"><a href="index.html" class="text-secondary">Volver a la tienda</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>