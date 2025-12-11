<?php
require_once 'config_sesion.php';
require_once 'Autenticacion.php';

$auth = new Autenticacion();
$auth->logout();

header("Location: login.php");
exit();
?>