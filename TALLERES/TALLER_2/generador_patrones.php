<?php
echo "<h2>Bucles con estructuras de control</h2>";

/* =======================
   BUCLE FOR + IF/ELSE
   ======================= */
echo "<h3>Clasificación de números del 1 al 10</h3>";
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) {
        echo "$i es par<br>";
    } else {
        echo "$i es impar<br>";
    }
}
echo "<br>";

/* =======================
   BUCLE WHILE + IF/ELSEIF/ELSE
   ======================= */
echo "<h3>Evaluación de calificaciones</h3>";
$notas = [95, 82, 67, 45, 100];
$index = 0;

while ($index < count($notas)) {
    $nota = $notas[$index];
    echo "Nota: $nota - ";

    if ($nota >= 90) {
        echo "Excelente<br>";
    } elseif ($nota >= 70) {
        echo "Aprobado<br>";
    } elseif ($nota >= 50) {
        echo "Regular<br>";
    } else {
        echo "Reprobado<br>";
    }

    $index++;
}
echo "<br>";

/* =======================
   BUCLE DO-WHILE + IF
   ======================= */
echo "<h3>Simulación de intentos de acceso</h3>";
$intento = 0;
$acceso = false;

do {
    $intento++;
    // Simulamos que el acceso es exitoso en el intento 3
    if ($intento == 3) {
        $acceso = true;
        echo "Intento $intento: Acceso concedido ✅<br>";
    } else {
        echo "Intento $intento: Acceso denegado ❌<br>";
    }
} while (!$acceso && $intento < 5);

if (!$acceso) {
    echo "Se alcanzó el número máximo de intentos sin éxito.<br>";
}
echo "<br>";
?>
