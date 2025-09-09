<?php
// procesador_frases.php
include 'operaciones_cadenas.php';

// Definir arreglo con frases
$frases = [
    "tres por tres es nueve",
    "la vida es bella",
    "php es un lenguaje de programacion",
    "me gusta programar en php"
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Procesador de Frases</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Resultados del Procesador de Frases</h2>
    <table>
        <tr>
            <th>Frase Original</th>
            <th>Palabras Repetidas</th>
            <th>Frase Capitalizada</th>
        </tr>
        <?php foreach ($frases as $frase): ?>
            <tr>
                <td><?php echo $frase; ?></td>
                <td>
                    <?php
                        $repetidas = contar_palabras_repetidas($frase);
                        foreach ($repetidas as $palabra => $cantidad) {
                            echo "$palabra = $cantidad<br>";
                        }
                    ?>
                </td>
                <td><?php echo capitalizar_palabras($frase); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
