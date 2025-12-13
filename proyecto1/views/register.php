<?php
// Inicia el buffer de salida
ob_start();

// Genera token CSRF
$csrf = generate_csrf();
?>

<h3>Registro</h3>

<?php if (!empty($error)): ?>
    <p style="color:#b00"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= e(BASE_URL) ?>/index.php?action=do_register">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

    <label>
        Nombre:<br>
        <input type="text" name="name" required>
    </label><br>

    <label>
        Email:<br>
        <input type="email" name="email" required>
    </label><br>

    <label>
        Contraseña:<br>
        <input type="password" name="password" required minlength="6">
    </label><br>

    <label>
        <input type="checkbox" name="is_organizer" value="1"> Soy organizador
    </label><br>

    <button type="submit">Registrar</button>
</form>

<p>
    ¿Ya tienes cuenta? 
    <a href="<?= e(BASE_URL) ?>/index.php?view=login">Inicia sesión</a>
</p>

<?php
// Captura el contenido y lo pasa al layout
$content = ob_get_clean();
require __DIR__ . '/layout.php';
