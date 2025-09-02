<?php

function leerInventario($archivo) {
    if (!file_exists($archivo)) {
        echo "<p style='color:red;'>El archivo $archivo no existe.</p>";
        return [];
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true);
}

function ordenarInventario($inventario) {
    usort($inventario, function ($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
    return $inventario;
}

function mostrarInventario($inventario) {
    echo "<h2>📦 Resumen del Inventario</h2>";
    echo "<table>";
    echo "<tr><th>Producto</th><th>Precio</th><th>Cantidad</th></tr>";
    foreach ($inventario as $producto) {
        echo "<tr>
                <td>{$producto['nombre']}</td>
                <td>\${$producto['precio']}</td>
                <td>{$producto['cantidad']}</td>
              </tr>";
    }
    echo "</table>";
}

function calcularValorTotal($inventario) {
    $total = array_sum(array_map(function ($producto) {
        return $producto['precio'] * $producto['cantidad'];
    }, $inventario));
    return $total;
}

function informeStockBajo($inventario) {
    $stockBajo = array_filter($inventario, function ($producto) {
        return $producto['cantidad'] < 5;
    });
    echo "<h2>⚠️ Informe de Stock Bajo</h2>";
    if (empty($stockBajo)) {
        echo "<p style='color:green;'>No hay productos con stock bajo.</p>";
    } else {
        echo "<table>";
        echo "<tr><th>Producto</th><th>Cantidad</th></tr>";
        foreach ($stockBajo as $producto) {
            echo "<tr>
                    <td>{$producto['nombre']}</td>
                    <td>{$producto['cantidad']}</td>
                  </tr>";
        }
        echo "</table>";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #444;
        }
        h2 {
            margin-top: 30px;
            color: #222;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 10px auto;
            background: #fff;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1> Sistema de Gestión de Inventario</h1>
    <?php
    $archivo = "inventario.json";
    $inventario = leerInventario($archivo);

    if (!empty($inventario)) {
        $inventario = ordenarInventario($inventario);
        mostrarInventario($inventario);

        $valorTotal = calcularValorTotal($inventario);
        echo "<h2>💰 Valor total del inventario: \$" . number_format($valorTotal, 2) . "</h2>";

        informeStockBajo($inventario);
    } else {
        echo "<p>No se pudo cargar el inventario.</p>";
    }
    ?>
</body>
</html>
