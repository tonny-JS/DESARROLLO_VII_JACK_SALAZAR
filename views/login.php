<?php
// Inicia el buffer de salida
ob_start();

// Genera token CSRF
$csrf = generate_csrf();
?>

<h3>Iniciar sesión</h3>

<?php if (!empty($error)): ?>
    <p style="color:#b00"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= e(BASE_URL) ?>/index.php?action=do_login">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

    <label>
        Email:<br>
        <input type="email" name="email" required>
    </label><br>

    <label>
        Contraseña:<br>
        <input type="password" name="password" required>
    </label><br>

    <button type="submit">Entrar</button>
</form>

<p>
    ¿No tienes cuenta? 
    <a href="<?= e(BASE_URL) ?>/index.php?view=register">Regístrate</a>
</p>

<?php
// Captura el contenido y lo pasa al layout
$content = ob_get_clean();
require __DIR__ . '/layout.php';
