<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<div class="register-form">
    <h2>Registro</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="index.php?action=do_register">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" placeholder="Nombre" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br>

        <label for="is_organizer">
            <input type="checkbox" id="is_organizer" name="is_organizer" value="1">
            Soy organizador
        </label><br>

        <button class="btn" type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? 
        <a href="index.php?view=login" class="btn">Inicia sesión</a>
    </p>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
