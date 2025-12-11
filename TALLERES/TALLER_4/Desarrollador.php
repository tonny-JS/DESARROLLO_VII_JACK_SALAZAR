<?php
// Desarrollador.php
require_once __DIR__ . '/Empleado.php';
require_once __DIR__ . '/Evaluable.php';


class Desarrollador extends Empleado implements Evaluable {
protected string $lenguaje;
protected string $nivel; // junior, mid, senior


public function __construct(string $nombre, int $id, float $salarioBase, string $lenguaje, string $nivel) {
parent::__construct($nombre, $id, $salarioBase);
$this->lenguaje = $lenguaje;
$this->nivel = $nivel;
}


public function getLenguaje(): string { return $this->lenguaje; }
public function setLenguaje(string $l): void { $this->lenguaje = $l; }


public function getNivel(): string { return $this->nivel; }
public function setNivel(string $n): void { $this->nivel = $n; }


// Implementación de evaluarDesempeno para Desarrollador
public function evaluarDesempeno(): array {
// Ejemplo: score basado en nivel
$baseScore = match (strtolower($this->nivel)) {
'junior' => rand(4, 7),
'mid', 'intermedio' => rand(6, 9),
'senior' => rand(7, 10),
default => rand(5, 8),
};


$comentario = "Desarrollador {$this->lenguaje} - nivel {$this->nivel}";


// Aumentar salario si score muy alto (demostración)
if ($baseScore >= 9) {
// aumento del 10% del salario base
$this->setSalarioBase($this->getSalarioBase() * 1.10);
$comentario .= ". Aumento de salario aplicado (10%).";
}


return ['score' => $baseScore, 'comentario' => $comentario];
}


public function toArray(): array {
$base = parent::toArray();
$base['tipo'] = 'Desarrollador';
$base['lenguaje'] = $this->lenguaje;
$base['nivel'] = $this->nivel;
return $base;
}


public static function fromArray(array $data): Desarrollador {
return new Desarrollador($data['nombre'], (int)$data['id'], (float)$data['salarioBase'], $data['lenguaje'] ?? '', $data['nivel'] ?? '');
}
}