<?php

class Empleado {
protected string $nombre;
protected int $id;
protected float $salarioBase;


public function __construct(string $nombre, int $id, float $salarioBase) {
$this->nombre = $nombre;
$this->id = $id;
$this->salarioBase = $salarioBase;
}


// Getters y Setters
public function getNombre(): string { return $this->nombre; }
public function setNombre(string $nombre): void { $this->nombre = $nombre; }


public function getId(): int { return $this->id; }
public function setId(int $id): void { $this->id = $id; }


public function getSalarioBase(): float { return $this->salarioBase; }
public function setSalarioBase(float $salarioBase): void { $this->salarioBase = $salarioBase; }


// Representación simple para serializar
public function toArray(): array {
return [
'tipo' => 'Empleado',
'nombre' => $this->nombre,
'id' => $this->id,
'salarioBase' => $this->salarioBase
];
}


public static function fromArray(array $data): Empleado {
return new Empleado($data['nombre'], (int)$data['id'], (float)$data['salarioBase']);
}
}