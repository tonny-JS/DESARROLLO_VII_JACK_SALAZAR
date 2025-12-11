<?php
require_once __DIR__ . '/common.php';

$action = $_GET['action'] ?? null;
$view   = $_GET['view'] ?? 'home';
$error  = null;

switch ($action) {
    case 'do_login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check($_POST['csrf'] ?? null);

            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            $stmt = $db->prepare(
                'SELECT id, name, email, password_hash 
                 FROM users 
                 WHERE email = :email 
                 LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($u && password_verify($pass, $u['password_hash'])) {
                $_SESSION['user'] = [
                    'id'    => (int)$u['id'],
                    'name'  => $u['name'],
                    'email' => $u['email']
                ];
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }

            $error = 'Credenciales inválidas';
            include __DIR__ . '/views/login.php';
        }
        break;

    case 'do_register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check($_POST['csrf'] ?? null);

            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $isOrg = isset($_POST['is_organizer']);

            // Validaciones básicas
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
                $error = 'Datos inválidos';
                include __DIR__ . '/views/register.php';
                break;
            }

            // Verificar si el email ya existe
            $exists = $db->prepare('SELECT 1 FROM users WHERE email = :e');
            $exists->execute([':e' => $email]);

            if ($exists->fetchColumn()) {
                $error = 'El email ya está registrado';
                include __DIR__ . '/views/register.php';
                break;
            }

            // Registrar usuario
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $db->beginTransaction();

            $ins = $db->prepare(
                'INSERT INTO users (name, email, password_hash) 
                 VALUES (:n, :e, :h)'
            );
            $ins->execute([':n' => $name, ':e' => $email, ':h' => $hash]);

            $uid = (int)$db->lastInsertId();

            // Si es organizador, insertarlo en tabla organizers
            if ($isOrg) {
                $org = $db->prepare('INSERT INTO organizers (user_id) VALUES (:uid)');
                $org->execute([':uid' => $uid]);
            }

            $db->commit();

            $_SESSION['user'] = [
                'id'    => $uid,
                'name'  => $name,
                'email' => $email
            ];

            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php');
        exit;
}
