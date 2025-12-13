<?php
require_once 'config_sesion.php';
require_once 'Autenticacion.php';
require_once 'Validador.php';

if (isset($_SESSION['usuario'])) {
    if (strtolower($_SESSION['usuario']['rol']) === 'profesor') {
        header("Location: panel_profesor.php");
    } else {
        header("Location: panel_estudiante.php");
    }
    exit();
}
$mensaje = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!Validador::verificarCsrf($_POST['csrf_token'] ?? '')) {
        $mensaje = "Error de validación CSRF";
    } else {
        $auth = new Autenticacion();
        [$ok, $msg] = $auth->login($_POST['usuario'] ?? '', $_POST['contrasena'] ?? '');
        if ($ok) {
            if (strtolower($_SESSION['usuario']['rol']) === 'profesor') {
                header("Location: panel_profesor.php");
            } else {
                header("Location: panel_estudiante.php");
            }
            exit();
        } else {
            $mensaje = $msg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if ($mensaje): ?>
        <p style="color:red;"><?php echo Validador::e($mensaje); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="usuario">Usuario:</label><br>
        <input type="text" id="usuario" name="usuario" required><br><br>

        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br><br>

        <input type="hidden" name="csrf_token" value="<?php echo Validador::e($_SESSION['csrf_token']); ?>">
        <input type="submit" value="Iniciar Sesión">
    </form>

    <br>
    <p><strong>Usuarios de prueba:</strong></p>
    <ul>
        <li>profesor / 12345</li>
        <li>estudiante / 12345</li>
    </ul>
</body>
</html>
