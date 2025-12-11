<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$config = require __DIR__ . '/config.php';
try {
$db = new PDO($config['db_dsn'], $config['db_user'], $config['db_pass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
http_response_code(500);
echo 'Database error';
exit;
}


define('BASE_URL', rtrim($config['base_url'], '/'));


function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }


function generate_csrf() {
if (empty($_SESSION['csrf_token'])) {
$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
return $_SESSION['csrf_token'];
}


function csrf_check($token) {
if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
http_response_code(400);
exit('CSRF token inválido');
}
}


function isLogged() {
return !empty($_SESSION['user']);
}


function userId() {
return $_SESSION['user']['id'] ?? null;
}


function require_login() {
if (!isLogged()) {
header('Location: ' . BASE_URL . '/index.php?view=login');
exit;
}
}


function is_organizer($db, int $uid): bool {
$stmt = $db->prepare('SELECT 1 FROM organizers WHERE user_id=:uid LIMIT 1');
$stmt->execute([':uid' => $uid]);
return (bool)$stmt->fetchColumn();
}