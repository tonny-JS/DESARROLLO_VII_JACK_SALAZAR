<?php
require_once __DIR__ . '/Empleado.php';
require_once __DIR__ . '/Gerente.php';
require_once __DIR__ . '/Desarrollador.php';
require_once __DIR__ . '/Evaluable.php';
class Empresa {
/** @var Empleado[] */
protected array $empleados = [];


public function agregarEmpleado(Empleado $e): void {
$this->empleados[$e->getId()] = $e;
}


/** @return Empleado[] */
public function listarEmpleados(): array { return array_values($this->empleados); }


public function calcularNominaTotal(): float {
$total = 0.0;
foreach ($this->empleados as $e) {
$s = $e->getSalarioBase();
// Si tiene método getBono, lo agregamos
if (method_exists($e, 'getBono')) {
$s += $e->getBono();
}
$total += $s;
}
return $total;
}


public function evaluarDesempenoTodos(): array {
$resultados = [];
foreach ($this->empleados as $e) {
if ($e instanceof Evaluable) {
$res = $e->evaluarDesempeno();
$resultados[$e->getId()] = array_merge(['nombre' => $e->getNombre()], $res);
} else {
$resultados[$e->getId()] = ['nombre' => $e->getNombre(), 'error' => 'No evaluable'];
}
}
return $resultados;
}


// Reportes opcionales
public function listarPorDepartamento(string $departamento): array {
return array_values(array_filter($this->empleados, function($e) use ($departamento) {
return ($e instanceof Gerente) && $e->getDepartamento() === $departamento;
}));
}


public function salarioPromedioPorTipo(string $tipo): float {
$filtrados = array_filter($this->empleados, function($e) use ($tipo) {
return match ($tipo) {
'Gerente' => $e instanceof Gerente,
'Desarrollador' => $e instanceof Desarrollador,
default => get_class($e) === $tipo
};
});


if (count($filtrados) === 0) return 0.0;
$suma = 0.0;
foreach ($filtrados as $f) {
$suma += $f->getSalarioBase() + (method_exists($f, 'getBono') ? $f->getBono() : 0);
}
return $suma / count($filtrados);
}


// Guardar y cargar empleados a/desde JSON (serialización simple)
public function guardarEnArchivo(string $ruta): bool {
$arr = [];
foreach ($this->empleados as $e) {
$arr[] = $e->toArray();
}
$json = json_encode($arr, JSON_PRETTY_PRINT);
return file_put_contents($ruta, $json) !== false;
}


}