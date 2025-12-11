<?php
session_start();
require_once 'validaciones.php';
require_once 'sanitizacion.php';

$errores = [];
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campos = ['nombre', 'email', 'sitio_web', 'genero', 'intereses', 'comentarios', 'fecha_nacimiento'];

    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            $valor = $_POST[$campo];
            $funcionBase = ucfirst(str_replace('_', '', $campo));
            $valorSanitizado = call_user_func("sanitizar" . $funcionBase, $valor);
            $datos[$campo] = $valorSanitizado;

            if (!call_user_func("validar" . $funcionBase, $valorSanitizado)) {
                $errores[] = "El campo $campo no es válido.";
            }
        }
    }

    // Calcular edad desde fecha de nacimiento
    if (isset($datos['fecha_nacimiento'])) {
        $fechaNacimiento = new DateTime($datos['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $fechaNacimiento->diff($hoy)->y;
        if ($edad < 18 || $edad > 120) {
            $errores[] = "La edad calculada ($edad años) no es válida.";
        } else {
            $datos['edad'] = $edad;
        }
    }

    // Procesar foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (!validarFotoPerfil($_FILES['foto_perfil'])) {
            $errores[] = "La foto de perfil no es válida.";
        } else {
            $nombreOriginal = basename($_FILES['foto_perfil']['name']);
            $nombreUnico = uniqid() . "_" . $nombreOriginal;
            $rutaDestino = 'uploads/' . $nombreUnico;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaDestino)) {
                $datos['foto_perfil'] = $rutaDestino;
            } else {
                $errores[] = "Hubo un error al subir la foto de perfil.";
            }
        }
    }

    // Mostrar resultados o errores
    if (empty($errores)) {
        echo "<h2>Datos Recibidos:</h2><table border='1' cellpadding='8'>";
        foreach ($datos as $campo => $valor) {
            echo "<tr><th>" . ucfirst(str_replace('_', ' ', $campo)) . "</th>";
            if ($campo === 'intereses') {
                echo "<td>" . implode(", ", $valor) . "</td>";
            } elseif ($campo === 'foto_perfil') {
                echo "<td><img src='$valor' width='100'></td>";
            } else {
                echo "<td>$valor</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        // Guardar en JSON
        $archivo = 'registros.json';
        $registros = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];
        $registros[] = $datos;
        file_put_contents($archivo, json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        echo "<h2>Errores:</h2><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";

        $_SESSION['datos_previos'] = $datos;
        $_SESSION['errores'] = $errores;
    }

    echo "<br><a href='formulario.html'>Volver al formulario</a>";
} else {
    echo "Acceso no permitido.";
}
?>
