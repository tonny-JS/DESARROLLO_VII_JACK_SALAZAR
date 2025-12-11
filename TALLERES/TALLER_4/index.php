<?php
// index.php - demostración
require_once __DIR__ . '/Empresa.php';

// Crear empresa
$empresa = new Empresa();

// Crear empleados
$g1 = new Gerente('Ana Gómez', 1, 2500.00, 'Ventas');
$g2 = new Gerente('Carlos Ruiz', 2, 2700.00, 'Operaciones');

$d1 = new Desarrollador('Laura Pérez', 10, 1500.00, 'PHP', 'senior');
$d2 = new Desarrollador('Miguel Torres', 11, 1200.00, 'JavaScript', 'mid');
$d3 = new Empleado('Empleado Base', 50, 800.00); // no evaluable

// Agregar a la empresa
$empresa->agregarEmpleado($g1);
$empresa->agregarEmpleado($g2);
$empresa->agregarEmpleado($d1);
$empresa->agregarEmpleado($d2);
$empresa->agregarEmpleado($d3);

// Mostrar lista
echo "<h2>Lista de Empleados</h2>\n<ul>";
foreach ($empresa->listarEmpleados() as $e) {
    echo '<li>(' . $e->getId() . ') ' . htmlspecialchars($e->getNombre()) . 
         ' - Salario base: ' . number_format($e->getSalarioBase(), 2) . "</li>\n";
}
echo "</ul>";

// Calcular nómina total
echo "<p><strong>Nómina total:</strong> " . number_format($empresa->calcularNominaTotal(), 2) . "</p>\n";

// Evaluar desempeño
echo "<h2>Evaluaciones</h2>\n<ul>";
$evals = $empresa->evaluarDesempenoTodos();
foreach ($evals as $id => $res) {
    if (isset($res['error'])) {
        echo "<li>($id) {$res['nombre']} - <em>{$res['error']}</em></li>\n";
    } else {
        echo "<li>($id) {$res['nombre']} - Score: {$res['score']} - {$res['comentario']}</li>\n";
    }
}
echo "</ul>";

// Guardar en archivo JSON
$archivo = __DIR__ . '/empleados.json';
$empresa->guardarEnArchivo($archivo);

echo "<p>Datos guardados en: empleados.json</p>";

// Reporte adicional
echo "<p>Salario promedio Desarrollador: " . number_format($empresa->salarioPromedioPorTipo('Desarrollador'), 2) . "</p>";

// Listar gerentes de Ventas
$gerentesVentas = $empresa->listarPorDepartamento('Ventas');
echo "<h3>Gerentes en Ventas</h3>\n<ul>";
foreach ($gerentesVentas as $g) {
    echo "<li>" . htmlspecialchars($g->getNombre()) . "</li>\n";
}
echo "</ul>";
?>