<?php
require_once __DIR__ . '/../common.php';

$user    = $_SESSION['user'] ?? null;
$orgFlag = $user ? is_organizer($db, (int)$user['id']) : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plataforma de Eventos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 0;
        }
        header, footer {
            background: #f5f5f5;
            padding: 10px 16px;
        }
        nav a {
            margin-right: 12px;
        }
        main {
            padding: 16px;
        }
        .right {
            float: right;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="<?= e(BASE_URL) ?>/index.php">Inicio</a>
            <a href="<?= e(BASE_URL) ?>/index.php?view=events">Eventos</a>

            <?php if ($user): ?>
                <a href="<?= e(BASE_URL) ?>/index.php?view=my_registrations">Mis inscripciones</a>

                <?php if ($orgFlag): ?>
                    <a href="<?= e(BASE_URL) ?>/index.php?view=create_event">Crear evento</a>
                    <a href="<?= e(BASE_URL) ?>/index.php?view=organizer_dashboard">Panel organizador</a>
                <?php endif; ?>

                <span class="right">
                    Bienvenido, <?= e($user['name'] ?? $user['email']) ?> |
                    <a href="<?= e(BASE_URL) ?>/index.php?action=logout">Salir</a>
                </span>
            <?php else: ?>
                <span class="right">
                    <a href="<?= e(BASE_URL) ?>/index.php?view=login">Entrar</a>
                    <a href="<?= e(BASE_URL) ?>/index.php?view=register">Registrarse</a>
                </span>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if (isset($content)) { echo $content; } ?>
    </main>

    <footer>
        © 2025 Plataforma de Eventos
    </footer>
</body>
</html>
