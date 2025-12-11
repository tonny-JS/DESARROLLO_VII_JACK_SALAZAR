<?php
include 'config_sesion.php';
include 'Autenticacion.php';
include 'RepositorioCalificaciones.php';
require_once 'Validador.php';

$auth = new Autenticacion();
$auth->requireLogin();

$usuario = $auth->usuarioActual();
if (!$usuario->esEstudiante()) {
    header("Location: panel_profesor.php");
    exit();
}

$nombreAlumno = $_SESSION['usuario']['alumno'] ?? $usuario->getNombre(); // fallback
$repo = new RepositorioCalificaciones();
$miNota = $repo->obtenerPorEstudiante($nombreAlumno);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Estudiante</title>
</head>
<body>
    <h2>Bienvenido/a, <?php echo Validador::e($usuario->getNombre()); ?> (Estudiante)</h2>
    <p>Sesión iniciada: <?php echo Validador::e($_SESSION['usuario']['login_at']); ?></p>

    <h3>Tu calificación</h3>
    <?php if ($miNota !== null): ?>
        <p><strong><?php echo Validador::e($nombreAlumno); ?></strong>: <?php echo Validador::e($miNota); ?></p>
    <?php else: ?>
        <p>No se encontró una calificación asociada.</p>
    <?php endif; ?>

    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>