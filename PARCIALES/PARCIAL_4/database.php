<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');      // reemplaza con tu usuario MySQL
define('DB_PASSWORD', '90335177');   // reemplaza con tu contraseña MySQL
define('DB_NAME', 'techparts_db');        // nombre de la base de datos del parcial

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}
?>
