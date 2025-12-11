<?php 
// Iniciamos el buffer de salida
ob_start(); 
?>
<div class="login-form">
    <h2>Iniciar sesión</h2>

    <form method="post" action="index.php?action=do_login">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <button class="btn" type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? 
        <a href="index.php?view=register" class="btn">Regístrate</a>
    </p>
</div>
<?php
// Guardamos el contenido del buffer en la variable $content
$content = ob_get_clean();

// Incluimos el layout principal
require 'views/layout.php';
?>
