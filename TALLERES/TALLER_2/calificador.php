<?php
$calificacion = 85; // Puedes cambiar este valor

// Paso 1: Determinar la letra
if ($calificacion >= 90) {
    $letra = 'A';
} elseif ($calificacion >= 80) {
    $letra = 'B';
} elseif ($calificacion >= 70) {
    $letra = 'C';
} elseif ($calificacion >= 60) {
    $letra = 'D';
} else {
    $letra = 'F';
}

// Paso 2: Imprimir la letra
echo "Tu calificación es $letra\n";

// Paso 3: Aprobado o Reprobado
if ($letra == 'A') {
    echo ", Aprobado\n";
} elseif ($letra == 'B') {
    echo ", Aprobado\n";
} elseif ($letra == 'C') {
    echo ", Aprobado\n";
} elseif ($letra == 'D') {
    echo ", Aprobado\n";
} else {
    echo ", Reprobado\n";
}

// Paso 4: Mensaje adicional con switch
switch ($letra) {
    case 'A':
        echo ", Excelente trabajo\n";
        break;
    case 'B':
        echo ", Buen trabajo\n";
        break;
    case 'C':
        echo ", Puedes mejorar\n";
        break;
    case 'D':
        echo ", Esfuérzate más\n";
        break;
    case 'F':
        echo ", Debes esforzarte más\n";
        break;
}
?>