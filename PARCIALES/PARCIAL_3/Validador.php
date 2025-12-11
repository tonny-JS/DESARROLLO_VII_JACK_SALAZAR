<?php
class Validador {
    public static function usuarioValido($usuario) {
        $usuario = trim($usuario);
        return (strlen($usuario) >= 3) && preg_match('/^[a-zA-Z0-9]+$/', $usuario);
    }

    public static function passwordValida($password) {
        return strlen($password) >= 5;
    }

    public static function verificarCsrf($tokenFormulario) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenFormulario);
    }

    public static function e($texto) {
        return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
    }
}
