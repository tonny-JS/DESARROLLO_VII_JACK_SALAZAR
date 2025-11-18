<?php
include 'config_sesion.php';
require_once 'Autenticacion.php';
require_once 'RepositorioCalificaciones.php';
require_once 'Validador.php';

$auth = new Autenticacion();
$auth->requireLogin();

$usuario = $auth->usuarioActual();
if (!$usuario->esProfesor()) {
    header("Location: panel_estudiante.php");
    exit();
}

$repo = new RepositorioCalificaciones();
$lista = $repo->obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Profesor</title>
</head>
<body>
    <h2>Bienvenido, <?php echo Validador::e($usuario->getNombre()); ?> (Profesor)</h2>
    <p>Sesión iniciada: <?php echo Validador::e($_SESSION['usuario']['login_at']); ?></p>

    <h3>Listado de calificaciones</h3>
    <table border="1" cellpadding="6">
        <tr><th>Estudiante</th><th>Calificación</th></tr>
        <?php foreach ($lista as $nombre => $nota): ?>
            <tr>
                <td><?php echo Validador::e($nombre); ?></td>
                <td><?php echo Validador::e($nota); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>