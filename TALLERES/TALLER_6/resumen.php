<?php
$archivo = 'registros.json';

if (!file_exists($archivo)) {
    echo "<h2>No hay registros disponibles.</h2>";
    exit;
}

$registros = json_decode(file_get_contents($archivo), true);

echo "<h2>Resumen de Registros</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Nombre</th><th>Email</th><th>Edad</th><th>Género</th><th>Intereses</th><th>Foto</th></tr>";

foreach ($registros as $registro) {
    echo "<tr>";
    echo "<td>{$registro['nombre']}</td>";
    echo "<td>{$registro['email']}</td>";
    echo "<td>{$registro['edad']}</td>";
    echo "<td>{$registro['genero']}</td>";
    echo "<td>" . implode(", ", $registro['intereses']) . "</td>";
    echo "<td><img src='{$registro['foto_perfil']}' width='80'></td>";
    echo "</tr>";
}
echo "</table>";
?>
