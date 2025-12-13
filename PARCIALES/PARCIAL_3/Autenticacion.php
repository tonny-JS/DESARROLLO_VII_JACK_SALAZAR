<?php
include 'Usuario.php';
include 'Validador.php';
class Autenticacion {
    private $usuarios = [
        'profesor'   => ['password' => '12345', 'rol' => 'Profesor'],
        'estudiante' => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'Ana'],
        'ana'        => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'Ana'],
        'juan'       => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'Juan'], 
        'carlos'     => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'Carlos'],
        'beatriz'    => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'Beatriz'],
        'david'      => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'David'],
        'jack'       => ['password' => '12345', 'rol' => 'Estudiante', 'alumno' => 'jack']
    ];

    public function login($usuario, $password) {
        if (!Validador::usuarioValido($usuario) || !Validador::passwordValida($password)) {
            return [false, "Credenciales no válidas"];
        }

        if (!isset($this->usuarios[$usuario])) {
            return [false, "Usuario o contraseña incorrectos."];
        }

        $registro = $this->usuarios[$usuario];

        if ($registro['password'] !== $password) {
            return [false, "Usuario o contraseña incorrectos."];
        }

        $u = new Usuario($usuario, $registro['rol']);
        $_SESSION['usuario'] = [
            'nombre'   => $u->getNombre(),
            'rol'      => $u->getRol(),
            'alumno'   => $registro['alumno'] ?? null, // para mapear a calificación del estudiante
            'login_at' => date("Y-m-d H:i:s")
        ];

        // Regenerar ID al autenticar (lo usaste antes)
        session_regenerate_id(true);

        return [true, "Autenticación correcta."];
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
    }

    public function usuarioActual() {
        if (isset($_SESSION['usuario']['nombre'], $_SESSION['usuario']['rol'])) {
            return new Usuario($_SESSION['usuario']['nombre'], $_SESSION['usuario']['rol']);
        }
        return null;
    }

    public function requireLogin() {
        if (!$this->usuarioActual()) {
            header("Location: login.php");
            exit();
        }
    }

}