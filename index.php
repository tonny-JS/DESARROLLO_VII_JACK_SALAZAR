<?php
// index.php - Router principal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Panama');

define('BASE_PATH', __DIR__ . '/');
session_start();

// Configuración y clases
$cfg = require BASE_PATH . 'config.php';
define('BASE_URL', $cfg['base_url'] ?? '');

require_once BASE_PATH . 'src/Database.php';
require_once BASE_PATH . 'src/Event.php';
require_once BASE_PATH . 'src/EventManager.php';
require_once BASE_PATH . 'src/User.php';
require_once BASE_PATH . 'src/AuthManager.php';
// Nuevos modelos
require_once BASE_PATH . 'src/Ticket.php';
require_once BASE_PATH . 'src/Registration.php';
require_once BASE_PATH . 'src/Access.php';

// Instancias
$eventManager = new EventManager();
$authManager  = new AuthManager();
$event        = new Event();
$ticket       = new Ticket();
$registration = new Registration();

$action = $_GET['action'] ?? null;
$view   = $_GET['view'] ?? 'events';

// Helpers de auth
function requireLogin() {
  if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '?view=login');
    exit;
  }
}
function requireOrganizer() {
  requireLogin();
  $db = (new Database())->pdo();
  $stmt = $db->prepare('SELECT * FROM organizers WHERE user_id=:uid LIMIT 1');
  $stmt->execute([':uid' => $_SESSION['user']['id']]);
  $org = $stmt->fetch();
  if (!$org) {
    header('Location: ' . BASE_URL . '?view=events&error=not_organizer');
    exit;
  }
  return $org['id'];
}

// Manejo de acciones
switch ($action) {
  case 'store_event':
    $organizer_id = requireOrganizer();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $eventManager->createFromPost($_POST);
      header('Location: ' . BASE_URL . '?view=events');
      exit;
    }
    break;

  case 'do_login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $u = $authManager->login($_POST['email'], $_POST['password']);
      if ($u) {
        $_SESSION['user'] = $u;
        header('Location: ' . BASE_URL . '?view=events');
        exit;
      }
      $error = 'Credenciales inválidas';
      require BASE_PATH . 'views/login.php';
    }
    break;

  case 'do_register':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $role_id = (isset($_POST['is_organizer']) && $_POST['is_organizer'] == 1) ? 2 : 3;
      $uid = $authManager->register([
        'name'     => $_POST['name'],
        'email'    => $_POST['email'],
        'password' => $_POST['password'],
        'role_id'  => $role_id
      ]);
      if ($role_id == 2) {
        $db = (new Database())->pdo();
        $stmt = $db->prepare('INSERT INTO organizers (user_id,organization_name,contact_phone) VALUES (:u,:org,:phone)');
        $stmt->execute([':u' => $uid, ':org' => NULL, ':phone' => NULL]);
      }
      $_SESSION['user'] = (new User())->find($uid);
      header('Location: ' . BASE_URL . '?view=events');
      exit;
    }
    break;

  case 'logout':
    session_destroy();
    header('Location: ' . BASE_URL . '?view=events');
    exit;

  case 'toggle_event':
    $organizer_id = requireOrganizer();
    if (!empty($_GET['id'])) {
      $db = (new Database())->pdo();
      $stmt = $db->prepare("SELECT status FROM events WHERE id=:id");
      $stmt->execute([':id' => $_GET['id']]);
      $ev = $stmt->fetch();
      if ($ev) {
        $newStatus = ($ev['status'] === 'published') ? 'draft' : 'published';
        $update = $db->prepare("UPDATE events SET status=:status WHERE id=:id");
        $update->execute([':status' => $newStatus, ':id' => $_GET['id']]);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
        exit;
      }
    }
    break;

  case 'delete_event':
    $organizer_id = requireOrganizer();
    if (!empty($_GET['id'])) {
      $db = (new Database())->pdo();
      $stmt = $db->prepare("DELETE FROM events WHERE id=:id");
      $stmt->execute([':id' => $_GET['id']]);
      header('Location: ' . BASE_URL . '?view=events');
      exit;
    }
    break;

  // Tickets, Registrations y Access → igual que tu versión (ya están correctos)
  // ...
}

// Manejo de vistas
switch ($view) {
  case 'login':
    require BASE_PATH . 'views/login.php';
    break;
  case 'register':
    require BASE_PATH . 'views/register.php';
    break;
  case 'create_event':
    $organizer_id = requireOrganizer();
    require BASE_PATH . 'views/event_form.php';
    break;
  case 'events':
  default:
    $events = $event->allPublished();
    require BASE_PATH . 'views/event_list.php';
    break;
  // resto de vistas igual que tu versión
}
