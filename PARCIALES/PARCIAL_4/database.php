<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');       // usuario por defecto en Laragon
define('DB_PASSWORD', '');           // contraseña vacía en Laragon
define('DB_NAME', 'techparts_db');   // nombre de la base de datos

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}
?>
