<?php
require_once __DIR__ . '/Empleado.php';
require_once __DIR__ . '/Evaluable.php';


class Gerente extends Empleado implements Evaluable {
protected string $departamento;
protected float $bono = 0.0; // bono asignado


public function __construct(string $nombre, int $id, float $salarioBase, string $departamento) {
parent::__construct($nombre, $id, $salarioBase);
$this->departamento = $departamento;
}


public function getDepartamento(): string { return $this->departamento; }
public function setDepartamento(string $d): void { $this->departamento = $d; }


public function asignarBono(float $monto): void { $this->bono = max(0, $monto); }
public function getBono(): float { return $this->bono; }


// Implementación de evaluarDesempeno para Gerente
public function evaluarDesempeno(): array {
// Lógica de ejemplo: score basado en antigüedad simulada y departamento
// Aquí devolvemos un score entre 0 y 10
$score = rand(6, 10); // simplificado para demo
$comentario = "Evaluación para gerente de {$this->departamento}";


// Asignar bono automático si score alto (ejemplo simple)
if ($score >= 9) {
$this->asignarBono($this->salarioBase * 0.15); // 15% del salario
$comentario .= ". Bono asignado automáticamente.";
}


return ['score' => $score, 'comentario' => $comentario];
}


public function toArray(): array {
$base = parent::toArray();
$base['tipo'] = 'Gerente';
$base['departamento'] = $this->departamento;
$base['bono'] = $this->bono;
return $base;
}


public static function fromArray(array $data): Gerente {
$g = new Gerente($data['nombre'], (int)$data['id'], (float)$data['salarioBase'], $data['departamento'] ?? '');
$g->asignarBono((float)($data['bono'] ?? 0));
return $g;
}
}