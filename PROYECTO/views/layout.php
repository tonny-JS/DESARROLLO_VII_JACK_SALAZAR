<?php
// views/layout.php - layout sencillo con navegación
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma de Gestión de Eventos</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/style.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Plataforma de Eventos</h1>
      <nav>
        <a href="index.php">Inicio</a>
        <a href="index.php?view=events">Eventos</a>
        <?php if ($user): ?>
          <a href="index.php?view=my_registrations">Mis inscripciones</a>
          <?php
            // verificar si el usuario es organizador
            $db = (new Database())->pdo();
            $stmt = $db->prepare('SELECT id FROM organizers WHERE user_id=:uid LIMIT 1');
            $stmt->execute([':uid' => $user['id']]);
            $org = $stmt->fetch();
          ?>
          <?php if ($org): ?>
            <a href="index.php?view=create_event">Crear evento</a>
            <a href="index.php?view=organizer_dashboard">Panel organizador</a>
          <?php endif; ?>
          <span>Bienvenido, <?php echo htmlspecialchars($user['name']); ?></span>
          <a href="index.php?action=logout">Salir</a>
        <?php else: ?>
          <a href="index.php?view=login">Entrar</a>
          <a href="index.php?view=register">Registrarse</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container">
    <?php echo $content; ?>
  </main>

  <footer>
    <div class="container">
      <p>&copy; 2025 Plataforma de Eventos</p>
      <nav class="footer-nav">
        <a href="index.php">Inicio</a> |
        <a href="index.php?view=events">Eventos</a> |
        <?php if ($user): ?>
          <a href="index.php?view=my_registrations">Mis inscripciones</a> |
          <?php if (!empty($org)): ?>
            <a href="index.php?view=create_event">Crear evento</a> |
            <a href="index.php?view=organizer_dashboard">Panel organizador</a> |
          <?php endif; ?>
          <a href="index.php?action=logout">Salir</a>
        <?php else: ?>
          <a href="index.php?view=login">Entrar</a> |
          <a href="index.php?view=register">Registrarse</a>
        <?php endif; ?>
      </nav>
    </div>
  </footer>

  <script src="<?php echo BASE_URL; ?>/public/assets/js/main.js"></script>
</body>
</html>
