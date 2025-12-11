<?php
// Función para contar palabras repetidas en una cadena
function contar_palabras_repetidas($texto) {
    $texto = strtolower(trim($texto));
    $palabras = explode(" ", $texto);
    // Array 
    $contador = [];
    foreach ($palabras as $palabra) {
        $palabra = trim($palabra);
        if ($palabra != "") {
            if (isset($contador[$palabra])) {
                $contador[$palabra]++;
            } else {
                $contador[$palabra] = 1;
            }
        }
    }
    return $contador;
}
// Función para capitalizar la primera letra de cada palabra en una cadena
function capitalizar_palabras($texto) {
    $palabras = explode(" ", strtolower(trim($texto)));
    $resultado = [];
    foreach ($palabras as $palabra) {
        if ($palabra != "") {
            $resultado[] = strtoupper(substr($palabra, 0, 1)) . substr($palabra, 1);
        }
    }
    // Unir palabras en cadena
    return implode(" ", $resultado);
}
?>
