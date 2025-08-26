<?php
// Definición de variables
$nombre_completo = "Jack salazar";
$edad = 25;
$correo = "jack.salazar@email.com";
$telefono = "+507 6000-1234";

// Definición de constante
define("OCUPACION", "Estudiante");

// Impresión usando diferentes métodos
echo "Hola, mi nombre es " . $nombre_completo . ".<br>";
print("Tengo $edad años.<br>");
printf("Mi correo es: %s<br>", $correo);
echo "Mi número de teléfono es: " . $telefono . "<br>";
print("Actualmente soy " . OCUPACION . ".<br>");

// Información de debugging
echo "<br><strong>Información técnica:</strong><br>";
var_dump($nombre_completo);
echo "<br>";
var_dump($edad);
echo "<br>";
var_dump($correo);
echo "<br>";
var_dump($telefono);
echo "<br>";
var_dump(OCUPACION);
?>