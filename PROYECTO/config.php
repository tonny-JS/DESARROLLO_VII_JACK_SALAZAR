<?php
// configuración de la aplicación y la BD.
// Usa variables de entorno si existen; de lo contrario, valores por defecto.
return [
  'db' => [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'name'    => getenv('DB_NAME') ?: 'evento_db7',
    'user'    => getenv('DB_USER') ?: 'root',
    'pass'    => getenv('DB_PASS') ?: '90335177',
    'charset' => 'utf8mb4'
  ],
  // Si tienes la variable de entorno BASE_URL la usará, si no, usa la ruta local
  'base_url' => getenv('BASE_URL') ?: 'http://localhost/DESARROLLO_VII_JACK_SALAZAR/PROYECTO/
'
];
?>