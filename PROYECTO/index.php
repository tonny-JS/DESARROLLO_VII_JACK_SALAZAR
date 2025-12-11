<?php
// index.php - Router principal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Panama');

define('BASE_PATH', __DIR__ . '/');
session_start();

// --- Configuración ---
$cfg = require BASE_PATH . 'config.php';
define('BASE_URL', $cfg['base_url'] ?? 'http://localhost/DESARROLLO_VII_JACK_SALAZAR/PROYECTO');

// --- Clases ---
require_once BASE_PATH . 'src/Database.php';
require_once BASE_PATH . 'src/Event.php';
require_once BASE_PATH . 'src/EventManager.php';
require_once BASE_PATH . 'src/User.php';
require_once BASE_PATH . 'src/AuthManager.php';
require_once BASE_PATH . 'src/Ticket.php';
require_once BASE_PATH . 'src/Registration.php';
require_once BASE_PATH . 'src/Access.php';

// --- Instancias ---
$db            = new Database();
$pdo           = $db->pdo();
$eventManager  = new EventManager();
$authManager   = new AuthManager();
$event         = new Event();
$ticket        = new Ticket();
$registration  = new Registration();

$action = $_GET['action'] ?? null;
$view   = $_GET['view'] ?? 'home';
$error  = null;

// =============== Helpers ===============
function requireLogin() {
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/index.php?view=login');
        exit;
    }
}

function requireOrganizer() {
    requireLogin();
    global $pdo;
    $stmt = $pdo->prepare('SELECT id FROM organizers WHERE user_id=:uid LIMIT 1');
    $stmt->execute([':uid' => $_SESSION['user']['id']]);
    $org = $stmt->fetch();
    if (!$org) {
        header('Location: ' . BASE_URL . '/index.php?view=events&error=not_organizer');
        exit;
    }
    return $org['id'];
}

function render_view(string $view_file, array $vars = []) {
    extract($vars);
    ob_start();
    require BASE_PATH . 'views/' . $view_file . '.php';
    $content = ob_get_clean();
    require BASE_PATH . 'views/layout.php';
}

// =============== Acciones ===============
switch ($action) {
    case 'do_login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = $authManager->login(trim($_POST['email']), $_POST['password']);
            if ($u) {
                $_SESSION['user'] = $u;
                header('Location: ' . BASE_URL . '/index.php?view=events');
                exit;
            }
            $error = 'Credenciales inválidas';
            render_view('login', ['error' => $error]);
        }
        break;

    case 'do_register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role_id = (!empty($_POST['is_organizer']) && $_POST['is_organizer'] == 1) ? 2 : 3;
            $uid = $authManager->register([
                'name'     => trim($_POST['name']),
                'email'    => trim($_POST['email']),
                'password' => $_POST['password'],
                'role_id'  => $role_id
            ]);

            if ($role_id == 2) {
                $stmt = $pdo->prepare('INSERT INTO organizers (user_id, organization_name, contact_phone) VALUES (:u, NULL, NULL)');
                $stmt->execute([':u' => $uid]);
            }

            $_SESSION['user'] = (new User())->find($uid);
            header('Location: ' . BASE_URL . '/index.php?view=events');
            exit;
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?view=events');
        exit;

    // --- Aquí van tus otras acciones como store_event, delete_event, etc ---
}

// --- Si no hay acción, renderizar la vista ---
switch ($view) {
    case 'events':
        render_view('events');
        break;
    case 'login':
        render_view('login', ['error' => $error]);
        break;
    case 'register':
        render_view('register');
        break;
    case 'my_registrations':
        requireLogin();
        render_view('my_registrations');
        break;
    case 'create_event':
        $organizer_id = requireOrganizer();
        render_view('create_event');
        break;
    case 'organizer_dashboard':
        $organizer_id = requireOrganizer();
        render_view('organizer_dashboard');
        break;
    default:
        render_view('home');
        break;
}
