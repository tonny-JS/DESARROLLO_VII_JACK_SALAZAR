<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/common.php';

$action = $_GET['action'] ?? null;
$view   = $_GET['view'] ?? 'home';
$error  = null;

switch ($action) {

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
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
            exit;
        }
        break;


    /*
    |--------------------------------------------------------------------------
    | REGISTRO
    |--------------------------------------------------------------------------
    */
    case 'do_register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check($_POST['csrf'] ?? null);

            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $isOrg = isset($_POST['is_organizer']);

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
                $error = 'Datos inválidos';
                include __DIR__ . '/views/register.php';
                exit;
            }

            $exists = $db->prepare('SELECT 1 FROM users WHERE email = :e');
            $exists->execute([':e' => $email]);

            if ($exists->fetchColumn()) {
                $error = 'El email ya está registrado';
                include __DIR__ . '/views/register.php';
                exit;
            }

            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->beginTransaction();

            $ins = $db->prepare(
                'INSERT INTO users (name, email, password_hash) 
                 VALUES (:n, :e, :h)'
            );
            $ins->execute([':n' => $name, ':e' => $email, ':h' => $hash]);

            $uid = (int)$db->lastInsertId();

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


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    case 'logout':
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php');
        exit;


    /*
    |--------------------------------------------------------------------------
    | CANCELAR INSCRIPCIÓN
    |--------------------------------------------------------------------------
    */
    case 'cancel_registration':
        require_login();

        $regId = intval($_GET['id'] ?? 0);
        $uid   = userId();

        if ($regId <= 0) {
            echo "ID inválido";
            exit;
        }

        $stmt = $db->prepare("
            SELECT id, status 
            FROM registrations 
            WHERE id = :id AND user_id = :uid
            LIMIT 1
        ");
        $stmt->execute([':id' => $regId, ':uid' => $uid]);
        $reg = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reg) {
            echo "Registro no encontrado o no te pertenece.";
            exit;
        }

        if ($reg['status'] === 'cancelled') {
            header("Location: ".BASE_URL."/index.php?view=my_registrations");
            exit;
        }

        $upd = $db->prepare("
            UPDATE registrations
            SET status = 'cancelled'
            WHERE id = :id
        ");
        $upd->execute([':id' => $regId]);

        header("Location: ".BASE_URL."/index.php?view=my_registrations");
        exit;
}



// ===================================================================
//   CARGA DE DATOS PARA LAS VISTAS (AQUÍ ESTABA TU FALTA IMPORTANTE)
// ===================================================================

if ($view === 'events') {

    // Cargar eventos publicados
    $stmt = $db->query("
        SELECT id, title, start_date, end_date
        FROM events
        WHERE status = 'published'
        ORDER BY start_date ASC
    ");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cargar la cantidad de tickets disponibles por evento
    foreach ($events as &$e) {
        $stmt2 = $db->prepare("
            SELECT SUM(quantity)
            FROM tickets
            WHERE event_id = :id
        ");
        $stmt2->execute([':id' => $e['id']]);
        $e['tickets_available'] = (int)$stmt2->fetchColumn();
    }
}



// =======================
//   CARGADOR DE VISTAS
// =======================

$viewFile = __DIR__ . '/views/' . $view . '.php';

if (file_exists($viewFile)) {
    include $viewFile;
} else {
    echo "<h1>Vista no encontrada: $view</h1>";
}
