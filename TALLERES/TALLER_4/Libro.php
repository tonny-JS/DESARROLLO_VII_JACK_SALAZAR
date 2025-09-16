<?php
require_once 'Prestable.php';

class Libro implements Prestable {
    private $titulo;
    private $autor;
    private $anio;
    private $disponible = true;

    public function __construct($titulo, $autor, $anio) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->anio = $anio;
    }

    public function obtenerInformacion() {
        return "Título: {$this->titulo}, Autor: {$this->autor}, Año: {$this->anio}";
    }

    public function prestar() {
        if ($this->disponible) {
            $this->disponible = false;
            return true;
        }
        return false;
    }

    public function devolver() {
        $this->disponible = true;
    }

    public function estaDisponible() {
        return $this->disponible;
    }
}

// Ejemplo de uso
$libro = new Libro("Rayuela", "Julio Cortázar", 1963);
echo $libro->obtenerInformacion() . "\n";
echo "Disponible: " . ($libro->estaDisponible() ? "Sí" : "No") . "\n";
$libro->prestar();
echo "Disponible después de prestar: " . ($libro->estaDisponible() ? "Sí" : "No") . "\n";
$libro->devolver();
echo "Disponible después de devolver: " . ($libro->estaDisponible() ? "Sí" : "No") . "\n";
