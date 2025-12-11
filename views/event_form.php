
<?php
require_once __DIR__.'/common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $isOrg = isset($_POST['is_organizer']) ? 1 : 0;

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $error = 'Datos inválidos';
    } else {
        // email único
        $exists = $db->prepare('SELECT 1 FROM users WHERE email=:e');
        $exists->execute([':e'=>$email]);
        if ($exists->fetchColumn()) {
            $error = 'El email ya está registrado';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->beginTransaction();
            $ins = $db->prepare('INSERT INTO users(name,email,password_hash) VALUES(:n,:e,:h)');
            $ins->execute([':n'=>$name, ':e'=>$email, ':h'=>$hash]);
            $uid = (int)$db->lastInsertId();

            if ($isOrg) {
                $org = $db->prepare('INSERT INTO organizers(user_id) VALUES(:uid)');
                $org->execute([':uid'=>$uid]);
            }
            $db->commit();
            $_SESSION['user'] = ['id'=>$uid,'name'=>$name,'email'=>$email];
            header('Location: '.BASE_URL.'index.php');
            exit;
        }
    }
}

ob_start();
?>
<h3>Registro</h3>
<?php if (!empty($error)): ?><p style="color:#b00"><?= e($error) ?></p><?php endif; ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
  <label>Nombre:<br><input type="text" name="name" required></label><br>
  <label>Email:<br><input type="email" name="email" required></label><br>
  <label>Contraseña:<br><input type="password" name="password" required minlength="6"></label><br>
  <label><input type="checkbox" name="is_organizer"> Soy organizador</label><br>
  <button type="submit">Registrar</button>
</form>
<p>¿Ya tienes cuenta? <?= e(BASE_URL) ?>index.php?view=loginInicia sesión</a></p>
<?php
$content = ob_get_clean();
include __DIR__.'/layout.php';
