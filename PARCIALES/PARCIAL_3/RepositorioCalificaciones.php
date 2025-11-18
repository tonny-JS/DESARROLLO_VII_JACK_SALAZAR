<?php
class RepositorioCalificaciones {
    private $calificaciones = [
        "Ana"     => 85,
        "Carlos"  => 92,
        "Beatriz" => 78,
        "David"   => 90,
        "Juan"    => 88,
        "jack"    => 71
    ];

    public function obtenerTodas() {
        // si uno quiere Puede ordenarlas utilizando asort:
        // asort($this->calificaciones);
        return $this->calificaciones;
    }

    public function obtenerPorEstudiante($nombre) {
        return $this->calificaciones[$nombre] ?? null;
    }
}