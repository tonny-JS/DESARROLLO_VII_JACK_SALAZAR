<?php
class Usuario {
    private $nombre;
    private $rol;

    public function __construct($nombre, $rol) {
        $this->nombre = trim($nombre);
        $this->rol = trim($rol);
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getRol() {
        return $this->rol;
    }

    public function esProfesor() {
        return strtolower($this->rol) === 'profesor';
    }

    public function esEstudiante() {
        return strtolower($this->rol) === 'estudiante';
    }
}